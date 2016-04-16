<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Needle
 *
 * @ORM\Table(name="needle")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NeedleRepository")
 */
class Needle
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="needle", type="string", length=255)
     */
    private $needle;

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
    public function getNeedle()
    {
        return $this->needle;
    }

    /**
     * @param mixed $needle
     */
    public function setNeedle($needle)
    {
        $this->needle = $needle;

        return $this;
    }
}

