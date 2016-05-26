<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Email;

class EmailRepository extends GeneralRepository
{
    public function create(array $data)
    {
        $obj = new Email();
        $this->setData($obj, $data);
        return $this->createObj($obj);
    }

    public function update(Email $obj, array $data)
    {
        $this->setData($obj, $data);
        return $this->updateObj($obj);
    }
}
