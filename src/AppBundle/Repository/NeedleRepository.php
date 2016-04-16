<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Community;
use AppBundle\Entity\Needle;

class NeedleRepository extends GeneralRepository
{
    public function create(array $data)
    {
        $this->_em->beginTransaction();
        try {
            $obj = new Needle();

            $this->setParams($obj, [
                'text'
            ], $data);

            $this->_em->persist($obj);

            $this->_em->flush($obj);
            $this->_em->commit();
        } catch (\Exception $e) {
            $this->_em->rollback();
            throw $e;
        }

        return $obj;
    }

    public function findByCommunity(Community $community)
    {
        return $this->_em->createQuery(
            'SELECT needle FROM AppBundle\Entity\Needle needle
              JOIN AppBundle\Entity\Bind bind WITH bind.needle = needle.id
              WHERE bind.community = :community_id'
        )->setParameters(['community_id' => $community->getId()])
            ->getResult();
    }
}
