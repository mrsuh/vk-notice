<?php

namespace AppBundle\Object;

class Message
{
    private $email;
    private $comments;
    private $email_hash_id;

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

    public function setEmailHashId($email_hash_id)
    {
        $this->email_hash_id = $email_hash_id;

        return true;
    }

    public function getEmailHashId()
    {
        return $this->email_hash_id;
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