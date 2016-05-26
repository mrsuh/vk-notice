<?php

namespace AppBundle\Object;

class HandledComment
{
    private $subway_ids;
    private $id;

    public function __construct($id)
    {
        $this->subway_ids = [];
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function addSubwayId($subway_id)
    {
        if(!in_array($subway_id, $this->subway_ids)) {
            $this->subway_ids[] = $subway_id;
        }

        return true;
    }

    public function getSubwayIds()
    {
        return $this->subway_ids;
    }

    public function setSubwayIds(array $subway_ids)
    {
        $this->subway_ids = $subway_ids;

        return true;
    }

    public function isEmptySubwayIds()
    {
        return empty($this->subway_ids);
    }
}