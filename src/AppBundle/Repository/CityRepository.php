<?php

namespace AppBundle\Repository;

use AppBundle\Entity\City;

class CityRepository extends GeneralRepository
{
    public function create(array $data)
    {
        $obj = new City();
        $this->setData($obj, $data);
        return $this->createObj($obj);
    }

}
