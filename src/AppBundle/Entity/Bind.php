<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Community
 *
 * @ORM\Table(name="bind")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BindRepository")
 */
class Bind
{
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Community")
     * @ORM\JoinColumn(name="community", referencedColumnName="id")
     */
    private $community;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Email")
     * @ORM\JoinColumn(name="email", referencedColumnName="id")
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Needle")
     * @ORM\JoinColumn(name="needle", referencedColumnName="id")
     */
    private $needle;

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
     * @return mixed
     */
    public function getCommunity()
    {
        return $this->community;
    }

    /**
     * @param mixed $community
     */
    public function setCommunity($community)
    {
        $this->community = $community;

        return $this;
    }

    /**
     * @return int
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param int $email
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return int
     */
    public function getNeedle()
    {
        return $this->needle;
    }

    /**
     * @param int $needle
     */
    public function setNeedle($needle)
    {
        $this->needle = $needle;

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
}

