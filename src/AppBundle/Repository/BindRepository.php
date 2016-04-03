<?php

namespace AppBundle\Repository;

class BindRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllAsArray()
    {
        return $this->createQueryBuilder('b')
            ->select('IDENTITY(b.community) as community_id')
            ->addSelect('b.email as email')
            ->addSelect('b.needle as needle')
            ->getQuery()->getResult();
    }

    public function create(array $data)
    {
        $this->_em->getConnection()->insert('bind', $data);
        return $this->_em->getConnection()->lastInsertId();
    }

    public function update($id, array $data)
    {
        $this->_em->getConnection()->update('bind', $data, ['id' => $id]);
    }
}
