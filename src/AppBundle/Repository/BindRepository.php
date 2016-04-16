<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Bind;

class BindRepository extends GeneralRepository
{
    public function create(array $data)
    {
        $this->_em->beginTransaction();
        try {
            $obj = new Bind();

            $this->setParams($obj, [
                'community',
                'email',
                'needle',
                'status'
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

    public function update(Bind $obj, array $data)
    {
        $this->_em->beginTransaction();
        try {

            $this->setParams($obj, [
                'community',
                'email',
                'needle',
                'status'
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
}
