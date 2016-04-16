<?php

namespace AppBundle\Service;

use AppBundle\C;
use AppBundle\Entity\Bind;
use AppBundle\Entity\Community;
use AppBundle\Entity\Needle;
use Doctrine\ORM\EntityManager;
use Mrsuh\VkApiBundle\Service\ApiService;

class Notify
{
    private $em;
    private $repo_community;
    private $repo_bind;
    private $repo_needle;
    private $mail;
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
        $this->repo_needle = $em->getRepository(C::REPO_NEEDLE);
        $this->mail = $mail;
        $this->user_id = $user_id;
    }

    /**
     * @return bool
     */
    public function run()
    {
        $this->em->beginTransaction();
        try {

            foreach ($this->repo_community->findAll() as $community) {

                $needles = $this->repo_needle->findByCommunity($community);

                $this->checkCommunityInfo($community);

                if (!$this->joinToCommunity($community)) {
                    continue;
                }

                if ($community->getLastCommentId()) {
                    $last_comment_id = $community->getLastCommentId();
                } else {
                    $last_comment_id = $this->getLastCommentIdFromCommunity($community);
                }

                while (true) {
                    $response = $this->api->call('board.getComments', [
                        'group_id' => $community->getGroupId(),
                        'topic_id' => $community->getTopicId(),
                        'start_comment_id' => $last_comment_id,
                        'count' => '100'
                    ]);

                    $comments = $response['response']['items'];

                    $current_comment_id = $this->getLastCommentIdFromComments($comments);
                    if ($current_comment_id === $last_comment_id) {
                        break;
                    }

                    $last_comment_id = $current_comment_id;

                    $this->handleComments($community, $needles, $comments);
                    break;
                }

//                $this->repo_community->update($community, ['last_comment_id' => $last_comment_id]);
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
     * @param array $comments
     * @param array Needle[]
     * @return array
     */
    private function handleComments(Community $community, array &$needles, array &$comments)
    {
        foreach ($needles as $needle) {
            foreach ($comments as $comment) {
                if (false === strrpos(mb_strtolower($comment['text']), $needle->getNeedle())) {
                    continue;
                }

                $this->mail->addToMailBox($community, $needle, $comment);
            }
        }
    }

    /**
     * @param array $comments
     * @return int
     */
    private function getLastCommentIdFromComments(array $comments)
    {
        $comment = array_pop($comments);
        return (integer)$comment['id'];
    }

    /**
     * @param Community $community
     * @return int
     * @throws \Mrsuh\VkApiBundle\Exception\VkApiRequestException
     */
    private function getLastCommentIdFromCommunity(Community $community)
    {
        $comments = $this->api->call('board.getComments', [
            'group_id' => $community->getGroupId(),
            'topic_id' => $community->getTopicId(),
            'sort' => 'desc',
            'count' => '20'
        ]);

        $last_comment = array_pop($comments['response']['items']);
        return (integer)$last_comment['id'];
    }

    /**
     * @param Community $community
     * @return bool
     * @throws \Mrsuh\VkApiBundle\Exception\VkApiRequestException
     */
    private function joinToCommunity(Community $community)
    {
        $join_status = false;

        switch($community->getStatus()){
            case Community::STATUS_GROUP_JOINED:
                $join_status = true;
                break;
            case Community::STATUS_GROUP_NOT_JOINED:
                $join = $this->api->call('groups.join', ['group_id' => $community->getGroupId()]);

                if (1 === $join['response']) {
                    $this->repo_community->update($community, ['status' => Community::STATUS_GROUP_JOINED]);
                    $join_status = true;
                } else {
                    $this->repo_community->update($community, ['status' => Community::STATUS_GROUP_NOT_JOINED]);
                }
                break;
            case null:
                $is_member = $this->api->call('groups.isMember', [
                    'group_id' => $community->getGroupId(),
                    'user_id' => $this->user_id
                ]);

                if (1 === $is_member['response']) {
                    $this->repo_community->update($community, ['status' => Community::STATUS_GROUP_JOINED]);
                    $join_status = true;
                }
                break;
            default:
                $join_status = false;

        }

       return $join_status;
    }

    private function checkCommunityInfo(Community $community)
    {
        if($community->getGroupName() && $community->getTopicName()){
            return false;
        }

        $response = $this->api->call('board.getTopics', [
            'group_id' => $community->getGroupId(),
            'topic_ids' => [$community->getTopicId()]
        ]);

        $topic_name = $response['response']['items'][0]['title'];

        $response = $this->api->call('groups.getById', [
            'group_id' => $community->getGroupId()
        ]);

        $group_name =  $response['response'][0]['name'];

        $this->repo_community->update($community, [
            'group_name' => $group_name,
            'topic_name' => $topic_name
        ]);
    }
}