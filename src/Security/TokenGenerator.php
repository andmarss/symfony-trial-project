<?php

namespace App\Security;

class TokenGenerator
{
    private const ALPHABET = 'ABCDEFGHIJKLMNOPRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    public function generateToken(int $length = 30): string
    {
       $maxNumber = strlen(static::ALPHABET);
       $token = '';

       for($i = 0; $i < $length; $i++) {
           $token .= static::ALPHABET[random_int(0, $maxNumber - 1)];
       }

       return $token;
    }
}