<?php

namespace AppBundle\Repository;

use AppBundle\Entity\City;
use AppBundle\Entity\Needle;

class NeedleRepository extends GeneralRepository
{
    public function create(array $data)
    {
        $obj = new Needle();
        $this->setData($obj, $data);
        return $this->createObj($obj);
    }

    public function findByCity(City $city)
    {
        return $this->createQueryBuilder('n')
            ->join('n.subway', 's')
            ->where('s.city = :city')
            ->setParameter('city', $city)
            ->getQuery()->getResult();
    }

    public function findBySubwayIds(array $ids)
    {
        return $this->createQueryBuilder('n')
            ->join('n.subway', 's')
            ->where('s.id in (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }
}
