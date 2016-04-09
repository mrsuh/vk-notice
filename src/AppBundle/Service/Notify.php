<?php

namespace AppBundle\Service;

use AppBundle\C;
use AppBundle\Entity\Community;
use Doctrine\ORM\EntityManager;
use Mrsuh\VkApiBundle\Service\ApiService;

class Notify
{
    private $em;
    private $repo_community;
    private $repo_bind;
    private $mail;
    private $emails;
    private $needles;
    private $api;
    private $user_id;

    /**
     * Search constructor.
     * @param EntityManager $em
     * @param ApiService $api
     * @param Mail $mail
     * @param $user_id
     */
    public function __construct(EntityManager $em, ApiService $api, Mail $mail, $user_id)
    {
        $this->em = $em;
        $this->api = $api;
        $this->repo_community = $em->getRepository(C::REPO_COMMUNITY);
        $this->repo_bind = $em->getRepository(C::REPO_BIND);
        $this->mail = $mail;
        $this->emails = [];
        $this->needles = [];
        $this->user_id = $user_id;
    }

    /**
     * @return bool
     */
    public function run()
    {
        $this->em->beginTransaction();
        try {
            $this->init();

            foreach ($this->repo_community->findAllAsArray() as $c) {
                $community_id = $c['id'];
                $group_id = $c['group_id'];
                $topic_id = $c['topic_id'];
                if (!array_key_exists($community_id, $this->needles)) {
                    continue;
                }

                if (!$this->joinToCommunity($community_id, $group_id, $c['status'])) {
                    continue;
                }

                if ($c['last_comment_id']) {
                    $last_comment_id = $c['last_comment_id'];
                } else {
                    $last_comment_id = $this->getLastCommentIdRemote($group_id, $topic_id);
                }

                while (true) {
                    $response = $this->api->call('board.getComments', [
                        'group_id' => $group_id,
                        'topic_id' => $topic_id,
                        'start_comment_id' => $last_comment_id,
                        'count' => '100'
                    ]);

                    $comments = $response['response']['items'];

                    $search_result = $this->search($comments, $this->needles[$community_id]);

                    $this->addToEmailBox($community_id, $search_result);

                    $current_comment_id = $this->getLastCommentIdLocal($comments);
                    if ($current_comment_id === $last_comment_id) {
                        break;
                    }

                    $last_comment_id = $current_comment_id;
                }

                $this->repo_community->update($community_id, ['last_comment_id' => $last_comment_id]);
            }

            $this->mail->send();

            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollback();
            throw new $e;
        }

        return true;
    }

    /**
     *
     */
    private function init()
    {
        foreach ($this->repo_bind->findAllAsArray() as $b) {
            $community_id = $b['community_id'];
            if (!array_key_exists($community_id, $this->needles)) {
                $this->needles[$community_id] = [];
            }
            $this->needles[$community_id][] = $b['needle'];

            $email = $b['email'];
            if (!array_key_exists($email, $this->emails)) {
                $this->emails[$email] = [];
            }
            $this->emails[$email][] = $b['needle'];
        }
    }

    /**
     * @param array $comments
     * @param array $needles
     * @return array
     */
    private function search(array $comments, array $needles)
    {
        $result = [];
        foreach ($needles as $needle) {
            foreach ($comments as $comment) {
                if (false !== strrpos(mb_strtolower($comment['text']), mb_strtolower($needle))) {
                    if (!array_key_exists($needle, $result)) {
                        $result[$needle] = [];
                    }
                    $result[$needle][] = $comment;
                }
            }
        }

        return $result;
    }

    /**
     * @param $community_id
     * @param array $result
     */
    private function addToEmailBox($community_id, array $result)
    {

        foreach ($this->emails as $email => $needles) {
            foreach ($needles as $needle) {

                if (!array_key_exists($needle, $result)) {
                    continue;
                }

                $this->mail->addToBox($email, $community_id, $needle, $result[$needle]);
            }
        }
    }

    /**
     * @param array $comments
     * @return int
     */
    private function getLastCommentIdLocal(array $comments)
    {
        $comment = array_pop($comments);
        return (integer)$comment['id'];
    }

    /**
     * @param $group_id
     * @param $topic_id
     * @return int
     * @throws \Mrsuh\VkApiBundle\Exception\VkApiRequestException
     */
    private function getLastCommentIdRemote($group_id, $topic_id)
    {
        $comments = $this->api->call('board.getComments', [
            'group_id' => $group_id,
            'topic_id' => $topic_id,
            'sort' => 'desc',
            'count' => '20'
        ]);

        $last_comment = array_pop($comments['response']['items']);
        return (integer)$last_comment['id'];
    }

    /**
     * @param $community_id
     * @param $group_id
     * @param $status
     * @return bool
     * @throws \Mrsuh\VkApiBundle\Exception\VkApiRequestException
     */
    private function joinToCommunity($community_id, $group_id, $status)
    {
        $joined_status = false;
        if (Community::STATUS_GROUP_NOT_JOINED === $status) {

            $joined = $this->api->call('groups.isMember', ['group_id' => $group_id, 'user_id' => $this->user_id]);

            if (1 !== $joined['response']) {
                $join = $this->api->call('groups.join', ['group_id' => $group_id]);

                if (1 === $join['response']) {
                    $this->repo_community->update($community_id, ['status' => Community::STATUS_GROUP_JOINED]);
                    $joined_status = true;
                }
            } else {
                $joined_status = true;
            }
        } else {
            $joined_status = true;
        }

        return $joined_status;
    }
}