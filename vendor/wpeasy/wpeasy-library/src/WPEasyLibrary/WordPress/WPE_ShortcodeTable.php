<?php


namespace WPEasyLibrary\WordPress;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use WPEasyLibrary\Helpers\View\ViewHelper;

class WPE_ShortcodeTable
{
    private $_twig;
    private $_shortcodesArray;

    public function __construct($shortcodesArray, $cache = false)
    {
        $this->_shortcodesArray = $shortcodesArray;
    }

    public function render()
    {
        ViewHelper::getView(
        	__DIR__ . '/View/shortcodeTable.phtml',
	        [
		        'shortcodes' => $this->_shortcodesArray
	        ],
	        true);
    }
}