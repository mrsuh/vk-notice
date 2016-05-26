<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Community;

class CommunityRepository extends GeneralRepository
{
    public function create(array $data)
    {
        $obj = new Community();
        $this->setData($obj, $data);
        return $this->createObj($obj);
    }

    public function update(Community $obj, array $data)
    {
        $this->setData($obj, $data);
        return $this->updateObj($obj);
    }
}
