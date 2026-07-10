<?php

namespace Ecoursity\App\Helpers;

class Str
{
    public static function random(int $length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    //slug
    public static function slug(string $string): string
    {
        //replace space to dash and lowercase
        $string = strtolower(str_replace(['-', ' ', '_'], '-', $string));
        //remove special character
        $string = preg_replace('/[^a-zA-Z0-9-]/', '', $string);
        //remove duplicate dash
        $string = str_replace('--', '-', $string);

        return $string;
    }
}
