<?php

namespace AppBundle\Repository;

class GeneralRepository extends \Doctrine\ORM\EntityRepository
{
    public function setParams($obj, array $params, array $data, $required = false)
    {
        foreach ($params as $v) {
            if (isset($data[$v]) && !is_null($data[$v])) {
                $s = 'set' . self::dashesToCamelCase($v);
                $obj->$s($data[$v]);
            } elseif ($required) {
                throw new \Exception('Required parameter: ' . $v . ' is empty');
            }
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
