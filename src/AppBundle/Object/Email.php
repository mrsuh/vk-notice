<?php

namespace AppBundle\Object;

class Email
{
    private $email;
    private $subways;

    public function __construct()
    {
        $this->subways = [];
    }

    public function addSubwayId($subway_id)
    {
        $this->subways[] = $subway_id;

        return true;
    }

    public function getSubwayIds()
    {
        return $this->subways;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return true;
    }

    public function getEmail()
    {
        return $this->email;
    }
}