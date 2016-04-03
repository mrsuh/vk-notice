<?php

namespace AppBundle\Repository;

class CommunityRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllAsArray()
    {
        return $this->createQueryBuilder('c')
            ->select('c.id as id')
            ->addSelect('c.groupId as group_id')
            ->addSelect('c.topicId as topic_id')
            ->addSelect('c.lastCommentId as last_comment_id')
            ->addSelect('c.status as status')
            ->getQuery()->getResult();
    }

    public function create(array $data)
    {
        $this->_em->getConnection()->insert('community', $data);
        return $this->_em->getConnection()->lastInsertId();
    }

    public function update($id, array $data)
    {
        $this->_em->getConnection()->update('community', $data, ['id' => $id]);
    }
}
