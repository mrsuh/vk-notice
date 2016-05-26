<?php

namespace AppBundle\Service;

use Hashids\Hashids;

class HashService extends Hashids
{
    /**
     * @param $hash
     * @return null | integer
     */
    public function decodeSingle($hash)
    {
        $decode = $this->decode($hash);

        if (isset($decode[0])) {
            return $decode[0];
        }

        return null;
    }
}