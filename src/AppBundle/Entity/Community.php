<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Community
 *
 * @ORM\Table(name="community")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommunityRepository")
 */
class Community
{
    const STATUS_GROUP_JOINED = 1;
    const STATUS_GROUP_NOT_JOINED = 0;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="group_id", type="integer")
     */
    private $groupId;

    /**
     * @var string
     *
     * @ORM\Column(name="group_name", type="string", length=255, nullable=true)
     */
    private $groupName;

    /**
     * @var string
     *
     * @ORM\Column(name="topic_id", type="integer")
     */
    private $topicId;

    /**
     * @var string
     *
     * @ORM\Column(name="topic_name", type="string", length=255, nullable=true)
     */
    private $topicName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_comment_id", type="integer", nullable=true)
     */
    private $lastCommentId;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * @return string
     */
    public function getTopicId()
    {
        return $this->topicId;
    }

    /**
     * @param string $topicId
     */
    public function setTopicId($topicId)
    {
        $this->topicId = $topicId;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastCommentId()
    {
        return $this->lastCommentId;
    }

    /**
     * @param string $lastCommentId
     */
    public function setLastCommentId($lastCommentId)
    {
        $this->lastCommentId = $lastCommentId;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * @param string $groupName
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;

        return $this;
    }

    /**
     * @return string
     */
    public function getTopicName()
    {
        return $this->topicName;
    }

    /**
     * @param string $topicName
     */
    public function setTopicName($topicName)
    {
        $this->topicName = $topicName;

        return $this;
    }
}

