<?php


namespace WPEasyLibrary\Helpers\Utils;


class ArrayUtils
{

    static function attributesArrayGetDefaults($arr)
    {
        $callback = function ($value) { return $value['default']; };
        return array_map( $callback , $arr);;
    }
}