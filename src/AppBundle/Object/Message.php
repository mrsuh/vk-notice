<?php

namespace AppBundle\Object;

class Message
{
    private $email;
    private $comments;

    public function __construct()
    {
        $this->email = null;
        $this->comments = [];
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

    public function addComment(HandledComment $comment)
    {
        $this->comments[] = $comment;

        return true;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function isEmptyComments()
    {
        return empty($this->comments);
    }
}