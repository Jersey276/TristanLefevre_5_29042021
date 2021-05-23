<?php

namespace core\auth;

class tokenGenerator
{
    /**
     * @param int $lenght : Permet de générer une chaine de caractères aléatoire de 60 caractères par défaut
     * @return String
     */
    public static function strRandom(int $lenght = 60): String
    {
        $alphabet = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        return substr(str_shuffle(str_repeat($alphabet, $lenght)), 0, $lenght);
    }
}
