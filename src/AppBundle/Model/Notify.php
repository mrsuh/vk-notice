<?php

namespace AppBundle\Model;

use AppBundle\C;
use AppBundle\Entity\Bind;
use AppBundle\Entity\City;
use AppBundle\Entity\Email;
use AppBundle\Exception\ValidationException;
use Doctrine\ORM\EntityManager;

class Notify
{
    private $repo_subway;
    private $repo_bind;
    private $repo_email;
    private $repo_needle;
    private $em;

    /**
     * Notify constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repo_bind = $em->getRepository(C::REPO_BIND);
        $this->repo_email = $em->getRepository(C::REPO_EMAIL);
        $this->repo_needle = $em->getRepository(C::REPO_NEEDLE);
        $this->repo_city = $em->getRepository(C::REPO_CITY);
        $this->repo_subway = $em->getRepository(C::REPO_SUBWAY);
    }

    public function subscribe($email_str, $city_id, array $home_types, array $subway_ids)
    {
        $this->checkEmail($email_str);
        $this->checkHomeTypes($home_types);

        $email = $this->getEmail($email_str);

        $city = $this->repo_city->findOneBy(['id' => $city_id]);
        $this->checkCity($city);

        $home_type = $this->getHomeTypeByTypes($home_types);

        $needles = $this->repo_needle->findBySubwayIds($subway_ids);

        $this->checkSubways($needles);

        $r = $subway_ids;
        foreach ($needles as $needle) {
           $r[] =  $this->repo_bind->create([
                    'email' => $email,
                    'status' => Bind::STATUS_ACTIVE,
                    'home_type' => $home_type,
                    'date' => new \DateTime(),
                    'city' => $city,
                    'subway' => $needle->getSubway(),
                    'needle' => $needle
                ]
            );
        }

        return $r;
    }

    public function unsubscribe(Email $email, $reason_status, $reason_text)
    {
        $this->em->beginTransaction();
        try {

            $this->repo_email->update($email, [
                'status' => $reason_status,
                'unsubscribe_reason' => $reason_text
            ]);

            $binds = $this->repo_bind->findBy(['email' => $email]);

            foreach($binds as $bind) {
                $this->repo_bind->update($bind, [
                    'status' => Bind::STATUS_DELETED
                ]);
            }

            $this->em->commit();
        } catch (\Exception $e){
            $this->em->rollback();
            throw $e;
        }

    }

    public function getSubscribedEmailById($id)
    {
     return $this->repo_email->findOneBy(['id' => $id, 'status' => Email::STATUS_SUBSCRIBED]);
    }

    public function getSubways()
    {
        return $this->repo_subway->findAll();
    }

    private function getHomeTypeByTypes(array $types)
    {

        if (2 === count($types)) {
            return Bind::TYPE_HOME_BOTH;
        }

        return $types[0];
    }

    /**
     * @param $email_str
     * @return \AppBundle\Entity\Email|null|object
     * @throws \Exception
     */
    private function getEmail($email_str)
    {
        $email = $this->repo_email->findOneBy(['email' => $email_str]);

        if (!$email) {
            $email = $this->repo_email->create(['email' => $email_str]);
        }

        return $email;
    }

    private function checkEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException('Email is not valid');
        };
    }

    private function checkCity($city)
    {
        if (!$city) {
            throw new ValidationException('City is not valid');
        };
    }

    private function checkSubways(array $subways)
    {
        if (!count($subways)) {
            throw new ValidationException('Subways is not valid');
        };
    }

    private function checkHomeTypes(array $types)
    {
        $home_types = [Bind::TYPE_HOME_BOTH, Bind::TYPE_HOME_FLAT, Bind::TYPE_HOME_ROOM];

        foreach ($types as $t) {
            if (!in_array($t, $home_types)) {
                throw new ValidationException('Home type is not valid');
            }
        }
    }
}