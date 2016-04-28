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
    private $repo_email;
    private $repo_needle;

    /**
     * Notify constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->repo_community = $em->getRepository(C::REPO_COMMUNITY);
        $this->repo_bind = $em->getRepository(C::REPO_BIND);
        $this->repo_email = $em->getRepository(C::REPO_EMAIL);
        $this->repo_needle = $em->getRepository(C::REPO_NEEDLE);
    }

    /**
     * @param $email_str
     * @param $link
     * @param $needle_str
     */
    public function register($email_str, $link, $needle_str)
    {
        preg_match('/topic-([0-9]+)_([0-9]+)/', $link, $matches);
        $needle_strs = explode(',', mb_strtolower($needle_str));

        $email = $this->getEmail($email_str);

        $community = $this->getCommunity($matches[1], $matches[2]);

        $this->setBind($email, $community, $needle_strs);
    }

    /**
     * @param $email
     * @param $community
     * @param array $needle_strs
     * @throws \Exception
     */
    private function setBind($email, $community, array $needle_strs)
    {
        foreach ($needle_strs as $needle_str) {

            $needle = $this->getNeedle(trim($needle_str));

            $exist = $this->repo_bind->findOneBy(
                [
                    'email' => $email,
                    'community' => $community,
                    'needle' => $needle
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
     * @param $needle_str
     * @return \AppBundle\Entity\Needle|null|object
     * @throws \Exception
     */
    private function getNeedle($needle_str)
    {
        $needle = $this->repo_needle->findOneBy(['needle' => $needle_str]);

        if (!$needle) {
            $needle = $this->repo_needle->create(['needle' => $needle_str]);
        }

        return $needle;
    }

    /**
     * @param $email_str
     * @return \AppBundle\Entity\Email|null|object
     * @throws \Exception
     */
    private function getEmail($email_str)
    {
        $email = $this->repo_email->findOneBy(['email' => $email_str]);

        if (!$email) {
            $email = $this->repo_email->create(['email' => $email_str]);
        }

        return $email;
    }

    /**
     * @param $group_id
     * @param $topic_id
     * @return Community|null|object
     * @throws \Exception
     */
    private function getCommunity($group_id, $topic_id)
    {
        $community = $this->repo_community->findOneBy(
            [
                'groupId' => $group_id,
                'topicId' => $topic_id
            ]);

        if (!$community) {
            $community = $this->repo_community->create(
                [
                    'group_id' => $group_id,
                    'topic_id' => $topic_id,
                    'status' => Community::STATUS_GROUP_NOT_JOINED
                ]);
        }

        return $community;
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