<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Needle
 *
 * @ORM\Table(name="email")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmailRepository")
 */
class Email
{
    const STATUS_SUBSCRIBED = 1;
    const STATUS_UNSUBSCRIBED_BY_THIS_APP = 2;
    const STATUS_UNSUBSCRIBED_BY_OTHER_APP = 3;
    const STATUS_UNSUBSCRIBED_BY_OTHER = 4;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status = self::STATUS_SUBSCRIBED;

    /**
     * @var int
     *
     * @ORM\Column(name="unsubscribed_reason", type="text", nullable=true)
     */
    private $unsubscribedReason;

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
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;

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
     * @return int
     */
    public function getUnsubscribedReason()
    {
        return $this->unsubscribedReason;
    }

    /**
     * @param int $unsubscribedReason
     */
    public function setUnsubscribedReason($unsubscribedReason)
    {
        $this->unsubscribedReason = $unsubscribedReason;

        return $this;
    }
}

