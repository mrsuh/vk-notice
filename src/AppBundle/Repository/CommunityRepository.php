<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Community;

class CommunityRepository extends GeneralRepository
{
    public function create(array $data)
    {
        $this->_em->beginTransaction();
        try {
            $obj = new Community();

            $this->setParams($obj, [
                'group_id',
                'topic_id',
                'last_comment_id',
                'status',
                'group_name',
                'topic_name',
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

    public function update(Community $obj, array $data)
    {
        $this->_em->beginTransaction();
        try {

            $this->setParams($obj, [
                'last_comment_id',
                'status',
                'group_name',
                'topic_name'
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
