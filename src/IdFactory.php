<?php

namespace RW\Helpers;

class IdFactory
{
    /**
     * Generate ID
     *
     * @param int $length
     * @return string
     */
    public static function generate($length = 6)
    {
        $id = bin2hex(openssl_random_pseudo_bytes($length));
        return $id;
    }
}