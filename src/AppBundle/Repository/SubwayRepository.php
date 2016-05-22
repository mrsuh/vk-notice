<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Subway;

class SubwayRepository extends GeneralRepository
{
    public function create(array $data)
    {
        $obj = new Subway();
        $this->setData($obj, $data);
        return $this->createObj($obj);
    }

    public function findAllNamesIndexById()
    {
        $subways = [];
        foreach($this->findAll() as $s){
            $subways[$s->getId()] = $s->getName();
        }

        return $subways;
    }
}
