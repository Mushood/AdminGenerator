<?php

namespace Sleekcube\AdminGenerator\Helpers;

class Pluraliser
{
    const SPECIALLAST = ['y'];

    public static function getPlural($word)
    {
        $lastLetter = $word[strlen($word) - 1];

        if (in_array($lastLetter, self::SPECIALLAST)) {
            $word[strlen($word) - 1] = "i";
            $plural = $word . "es";
        } else {
            $plural = $word . "s";
        }

        return $plural;
    }
}
