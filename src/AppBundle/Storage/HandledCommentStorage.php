<?php

namespace AppBundle\Storage;

use AppBundle\Object\HandledComment;

class HandledCommentStorage
{
    private $comments;

    public function __construct()
    {
        $this->comments = [];
    }

    public function add(HandledComment $comment)
    {
       $this->comments[] = $comment;

        return true;
    }

    public function getAll()
    {
        return $this->comments;
    }
}