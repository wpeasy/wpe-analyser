<?php
namespace WPEasyLibrary\Helpers\View;

class ViewHelper
{
    /**
     * @param $path
     * @param array $attsArray array of attributes to be extracted and made available to the view.
     * @param bool $echo
     * @return false|string
     */
    public static function getView($path, $attsArray = [], $echo = false)
    {
        $out = '';
        extract($attsArray); //Make Atts available to view.
        ob_start();
        require $path;
        $out = ob_get_contents();
        ob_end_clean();

        if($echo) echo $out;

        return $out;
    }
}