<?php

namespace core\auth;

class TokenGenerator
{
    /**
     * @param int length of random string generated
     * @return String token key
     */
    public static function strRandom(int $length = 60): String
    {
        $alphabet = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        return substr(str_shuffle(str_repeat($alphabet, $length)), 0, $length);
    }
}
