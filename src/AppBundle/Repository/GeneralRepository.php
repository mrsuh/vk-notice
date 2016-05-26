<?php

namespace AppBundle\Repository;

class GeneralRepository extends \Doctrine\ORM\EntityRepository
{
    public function createObj($obj)
    {
        $this->_em->beginTransaction();
        try{
            $this->_em->persist($obj);
            $this->_em->flush($obj);
            $this->_em->commit();
        } catch(\Exception $e){
            $this->_em->rollback();
            throw $e;
        }

        return $obj;
    }

    public function updateObj($obj)
    {
        $this->_em->beginTransaction();
        try{
            $this->_em->flush($obj);
            $this->_em->commit();
        } catch(\Exception $e){
            $this->_em->rollback();
            throw $e;
        }

        return $obj;
    }

    public function setData($obj, array $data)
    {
        foreach($data as $field => $value) {
            $s = 'set' . self::dashesToCamelCase($field);
            $obj->$s($value);
        }
    }

    private function dashesToCamelCase($string, $littleFirstLetter = false)
    {
        $words = explode('_', $string);
        if ($littleFirstLetter) {
            $str = array_shift($words);
        } else {
            $str = '';
        }
        foreach ($words as $w) {
            $str .= str_replace(' ', '', (ucfirst($w)));
        }
        return $str;
    }

}
