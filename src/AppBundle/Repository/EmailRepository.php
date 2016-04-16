<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Community;
use AppBundle\Entity\Email;
use AppBundle\Entity\Needle;

class EmailRepository extends GeneralRepository
{
    public function create(array $data)
    {
        $this->_em->beginTransaction();
        try {
            $obj = new Email();

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

    public function findByCommunityAndNeedle(Community $community, Needle $needle)
    {
        return $this->_em->createQuery(
            'SELECT email FROM AppBundle\Entity\Email email
              JOIN AppBundle\Entity\Bind bind WITH bind.email = email.id
              WHERE bind.community = :community_id AND bind.needle = :needle_id'
        )->setParameters(['community_id' => $community->getId(), 'needle_id' => $needle->getId()])
            ->getSingleResult();
    }
}
