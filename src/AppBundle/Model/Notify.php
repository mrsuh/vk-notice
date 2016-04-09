<?php

namespace AppBundle\Model;

use AppBundle\C;
use AppBundle\Entity\Bind;
use AppBundle\Entity\Community;
use Doctrine\ORM\EntityManager;

class Notify
{
    private $repo_community;
    private $repo_bind;

    /**
     * Notify constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->repo_community = $em->getRepository(C::REPO_COMMUNITY);
        $this->repo_bind = $em->getRepository(C::REPO_BIND);
    }

    /**
     * @param $email
     * @param $link
     * @param $needle
     */
    public function register($email, $link, $needle)
    {
        preg_match('/topic-([0-9]+)_([0-9]+)/', $link, $matches);
        $community_id = $this->getCommunityId($matches[1], $matches[2]);
        $needles = explode(',', mb_strtolower($needle));

        $this->setBind($email, $community_id, $needles);
    }

    /**
     * @param $email
     * @param $community_id
     * @param array $needles
     */
    private function setBind($email, $community_id, array $needles)
    {
        foreach ($needles as $n) {
            $exist = $this->repo_bind->findOneBy(
                [
                    'email' => $email,
                    'community' => $community_id,
                    'needle' => trim($n)
                ]);

            if ($exist) {
                if (Bind::STATUS_DELETED === $exist->getStatus()) {
                    $this->repo_bind->update($exist->getId(), [
                        'status' => Bind::STATUS_ACTIVE
                    ]);
                }
                continue;
            }

            $this->repo_bind->create([
                'email' => $email,
                'community' => $community_id,
                'needle' => $n,
                'status' => Bind::STATUS_ACTIVE
            ]);
        }
    }

    /**
     * @param $group_id
     * @param $topic_id
     * @return int|string
     */
    private function getCommunityId($group_id, $topic_id)
    {
        $community = $this->repo_community->findOneBy(
            [
                'groupId' => $group_id,
                'topicId' => $topic_id
            ]);

        if (!$community) {
            $community_id = $this->repo_community->create(
                [
                    'group_id' => $group_id,
                    'topic_id' => $topic_id,
                    'status' => Community::STATUS_GROUP_NOT_JOINED
                ]);
        } else {
            $community_id = $community->getId();
        }

        return $community_id;
    }

    /**
     * @param $email
     * @return bool
     */
    public function isValidEmail($email)
    {
        return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @param $data
     * @return bool
     */
    public function isEmpty($data)
    {
        return empty($data);
    }

    /**
     * @param $link
     * @return bool
     */
    public function isValidLink($link)
    {
        return 1 === preg_match('/topic-[0-9]*_[0-9]*/', $link);
    }
}