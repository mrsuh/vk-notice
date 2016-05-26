<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Bind;

class BindRepository extends GeneralRepository
{
    public function create(array $data)
    {
        $obj = new Bind();
        $this->setData($obj, $data);
        return $this->createObj($obj);
    }

    public function update(Bind $obj, array $data)
    {
        $this->setData($obj, $data);
        return $this->updateObj($obj);
    }
}
