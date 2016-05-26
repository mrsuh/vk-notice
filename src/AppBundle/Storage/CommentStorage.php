<?php

namespace AppBundle\Storage;

class CommentStorage
{
    private $comments;

    public function __construct()
    {
        $this->comments = [];
    }

    public function set($comment_id, $comment)
    {
        if (array_key_exists($comment_id, $this->comments)) {
            return false;
        }

        $this->comments[$comment_id] = $comment;

        return true;
    }

    public function get($comment_id)
    {
        if (!array_key_exists($comment_id, $this->comments)) {
            return null;
        }

        return $this->comments[$comment_id];
    }

    public function getAll()
    {
        return $this->comments;
    }
}