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

    public function addSubwayIdAndHomeType($subway_id, $home_type)
    {
        $this->subways[$subway_id] = $home_type;

        return true;
    }

    public function getSubwayIdAndHomeType()
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