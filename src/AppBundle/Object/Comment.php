<?php

namespace AppBundle\Object;

class Comment
{
    private $id;
    private $user_id;
    private $timestamp;
    private $text;
    private $photos;
    private $type;

    const TYPE_FLAT = 1;
    const TYPE_ROOM = 2;

    const ATTACH_PHOTO = 'photo';

    public function __construct
    (
        $id,
        $user_id,
        $timestamp,
        $text,
        array $photos,
        $type
    )
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->timestamp = $timestamp;
        $this->text = $text;
        $this->photos = $photos;
        $this->type = $type;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getPhotos()
    {
        return $this->photos;
    }

    public function getType()
    {
        return $this->type;
    }
}