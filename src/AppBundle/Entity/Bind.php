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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\City")
     * @ORM\JoinColumn(name="city", referencedColumnName="id")
     */
    private $city;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Subway")
     * @ORM\JoinColumn(name="subway", referencedColumnName="id")
     */
    private $subway;


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

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubway()
    {
        return $this->subway;
    }

    /**
     * @param mixed $subway
     */
    public function setSubway($subway)
    {
        $this->subway = $subway;

        return $this;
    }
}

