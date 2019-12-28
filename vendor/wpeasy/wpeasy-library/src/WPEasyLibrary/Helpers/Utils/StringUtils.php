<?php


namespace WPEasyLibrary\Helpers\Utils;


class StringUtils
{
    static function dashToCamelCase($string, $capitalizeFirstCharacter = false)
    {
        $str = str_replace('_', '', ucwords($string, '_'));
        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }
        return $str;
    }

    static function stringToDataType($string)
    {
        $lower = strtolower($string);
        if(in_array($lower, ['true','false'])){
            return filter_var($lower, FILTER_VALIDATE_BOOLEAN);
        }elseif(is_numeric($string)){
            return strpos($string,'.') === false? (int)$string : (float)$string ;
        }

        return $string;
    }
}