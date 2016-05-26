<?php

namespace AppBundle\Service;

use AppBundle\C;
use AppBundle\Entity\City;
use AppBundle\Entity\Community;
use AppBundle\Object\Comment;
use AppBundle\Object\HandledComment;
use AppBundle\Object\Note;
use AppBundle\Storage\CommentStorage;
use AppBundle\Storage\HandledCommentStorage;
use Doctrine\ORM\EntityManager;
use Mrsuh\VkApiBundle\Service\ApiService;

class Notify
{
    private $em;
    private $repo_community;
    private $repo_bind;
    private $repo_needle;
    private $repo_city;
    private $storage_comment;
    private $storage_handled_comment;
    private $mail;
    private $api;
    private $user_id;


    public function __construct(
        EntityManager $em,
        ApiService $api,
        Mail $mail,
        HandledCommentStorage $storage_handled_comment,
        CommentStorage $storage_comment,
        $user_id
    )
    {
        $this->em = $em;
        $this->api = $api;
        $this->repo_community = $em->getRepository(C::REPO_COMMUNITY);
        $this->repo_bind = $em->getRepository(C::REPO_BIND);
        $this->repo_needle = $em->getRepository(C::REPO_NEEDLE);
        $this->repo_city = $em->getRepository(C::REPO_CITY);
        $this->mail = $mail;
        $this->user_id = $user_id;
        $this->storage_handled_comment = $storage_handled_comment;
        $this->storage_comment = $storage_comment;
    }

    /**
     * @return bool
     */
    public function run()
    {
        $cities = $this->repo_city->findAll();

        foreach ($cities as $city) {
            $needles = $this->repo_needle->findByCity($city);

            foreach ($this->repo_community->findBy(['city' => $city]) as $community) {

                if (!$this->joinToCommunity($community)) {
                    continue;
                }

                $this->checkCommunityInfo($community);

                $last_comment_id = $this->getLastCommentId($community);

                while (true) {

                    $comments = $this->getComments($community, $last_comment_id);

                    $last_comment = end($comments);
                    $current_comment_id = (integer)$last_comment['id'];

                    if ($current_comment_id === $last_comment_id) {
                        break;
                    }

                    $last_comment_id = $current_comment_id;

                    $this->handleComments($city, $needles, $comments);
                }

                $this->repo_community->update($community, ['last_comment_id' => $last_comment_id]);
            }

            $this->mail->handle($city);
        }

        return true;
    }

    private function getComments(Community $community, $last_comment_id)
    {
        $response = $this->api->call('board.getComments', [
            'group_id' => $community->getGroupId(),
            'topic_id' => $community->getTopicId(),
            'start_comment_id' => $last_comment_id,
            'count' => '100'
        ]);

        return $response['response']['items'];
    }


    private function handleComments(City $city, array &$needles, array &$comments)
    {
        foreach ($comments as $comment) {
            $comment_id = $city->getId() . $comment['id'];
            $handled_comment = new HandledComment($comment_id);
            $handled_text = mb_strtolower($comment['text']);

            foreach ($needles as $needle) {
                if (false === strrpos($handled_text, $needle->getNeedle())) {
                    continue;
                }
                $handled_comment->addSubwayId($needle->getSubway()->getId());
            }

            if (!$handled_comment->isEmptySubwayIds()) {

                $photos = [];
                if (array_key_exists('attachments', $comment)) {
                    foreach ($comment['attachments'] as $attach) {
                        if (Comment::ATTACH_PHOTO !== $attach['type']) {
                            continue;
                        }

                        $photos[] = $attach['photo']['photo_604'];
                    }
                }

                $this->storage_handled_comment->add($handled_comment);
                $this->storage_comment->set($comment_id, new Comment(
                    $comment['id'],
                    $comment['from_id'],
                    $comment['date'],
                    $comment['text'],
                    $photos,
                    $this->getCommentType($handled_text)
                ));
            } else {
                unset($handled_comment);
            }
        }
    }

    private function getCommentType(&$text)
    {
        $room_list = ['комнату', 'комната', 'комнаты'];
        $flat_list = ['квартиру', 'квартира', 'кв.', 'кв ', 'кв-'];

        $room_pos = PHP_INT_MAX;
        foreach ($room_list as $l) {

            $pos = strpos($text, $l);
            $pos = $pos !== false ? $pos : PHP_INT_MAX;

            if ($pos < $room_pos) {
                $room_pos = $pos;
            }
        }

        $flat_pos = PHP_INT_MAX;
        foreach ($flat_list as $l) {
            $pos = strpos($text, $l);
            $pos = $pos !== false ? $pos : PHP_INT_MAX;

            if ($pos < $flat_pos) {
                $flat_pos = $pos;
            }
        }

        if (PHP_INT_MAX === $flat_pos && PHP_INT_MAX === $room_pos) {
            return Comment::TYPE_FLAT;
        }

        if ($flat_pos <= $room_pos) {
            return Comment::TYPE_FLAT;
        } else {
            return Comment::TYPE_ROOM;
        }
    }

    public function getLastCommentId(Community $community)
    {
        if ($community->getLastCommentId()) {
            $last_comment_id = $community->getLastCommentId();
        } else {
            $comments = $this->api->call('board.getComments', [
                'group_id' => $community->getGroupId(),
                'topic_id' => $community->getTopicId(),
                'sort' => 'desc',
                'count' => '20'
            ]);

            $last_comment = array_pop($comments['response']['items']);
            $last_comment_id = (integer)$last_comment['id'];
        }

        return $last_comment_id;
    }

    /**
     * @param Community $community
     * @return bool
     * @throws \Mrsuh\VkApiBundle\Exception\VkApiRequestException
     */
    private function joinToCommunity(Community $community)
    {
        $join_status = false;

        switch ($community->getStatus()) {
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
            default:
                $join_status = false;

        }

        return $join_status;
    }

    private function checkCommunityInfo(Community $community)
    {
        if ($community->getGroupName() && $community->getTopicName()) {
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

        $group_name = $response['response'][0]['name'];

        $this->repo_community->update($community, [
            'group_name' => $group_name,
            'topic_name' => $topic_name
        ]);
    }
}