<?php

namespace Sleekcube\AdminGenerator\Helpers;

class StringManipulator
{
    public static function genericReplace($content, $model)
    {
        $content = str_replace("Entitygenerator", self::pascalize($model), $content);
        $content = str_replace("entitygenerator", $model, $content);

        return $content;
    }

    public static function fileReplace($filePath, $key)
    {
        $boilerplate = file_get_contents($filePath);
        $boilerplate = self::genericReplace($boilerplate, $key);

        return $boilerplate;
    }

    public static function camelize($string)
    {
        return str_replace("_", "", lcfirst(ucwords($string, "_")));
    }

    public static function pascalize($string)
    {
        return str_replace("_", "", ucwords($string, "_"));
    }
}
