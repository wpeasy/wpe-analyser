<?php

/*
 * Sets up a root Application to be used by all WPEasy plugins
 *
 * Common styles and scripts will be found in https://www.wpeasy.net/ext
 * @See the WPEasy project
 *
 */

namespace WPEasyLibrary\WordPress;


use WPEasyLibrary\Helpers\View\ViewHelper;

class WPEasyApplication
{


    private static $_init;
    static $loadedPlugins = [];
    const TEMPLATE_DIR = __DIR__ . '/_templateCache';
    const MENU_PAGE_TITLE = 'WPEasy Options';
    const MENU_PAGE_NAME = 'WPEasy';
    const MENU_PAGE_PERMISSIONS = 'manage_options';
    const MENU_PAGE_SLUG = 'wpe-menu';
    const WPEASY_EXTERNAL_URL = 'https://www.wpeasy.net/ext/';
    const ADMIN_SCRIPT_SLUG = 'wpe-admin';
    const COMMON_SCRIPT_SLUG = 'wpe-common';
    const FRONTEND_SCRIPT_SLUG = 'wpe-frontend';

    static $firstCallingPluginConf;

    /*
     * Initialised by the first plugin using this library
     * This library is included in every WPEasy plugin code
     * Only the first loaded plugin will initialise this class
     * All js and css assets will be referenced for the plugin's assets
     */
    static function init($callingPluginConfig)
    {
        if (self::$_init) return;
        self::$_init = true;

        //Set the default PHP timezone based on WordPress settings
        date_default_timezone_set(TimezoneHelper::getTimezoneString());

        self::$firstCallingPluginConf = $callingPluginConfig;

        add_action('admin_menu', [__CLASS__, 'adminMenuTop'], 1);
        add_action('admin_enqueue_scripts', [__CLASS__, 'admin_enqueue_scripts'], 1);
        add_action('wp_enqueue_scripts', [__CLASS__, 'wp_enqueue_scripts'], 1);

        add_action('plugins_loaded', [__CLASS__, 'initPlugins']);

    }

    static function registerLoadedPlugin($config)
    {
    	self::$loadedPlugins[$config['pluginName']] = $config;
    }

    static function initPlugins()
    {
    	ksort(self::$loadedPlugins);
    	foreach(self::$loadedPlugins as $name=>$config)
	    {
	    	$controller = $config['pluginController'];
	    	$controller::init($config);
	    	ksort($config['modules']);
	    	foreach($config['modules'] as $module){
	    		$module::init();
		    }
	    }
    }

    static function admin_enqueue_scripts()
    {
        $callingPluginURL = self::$firstCallingPluginConf['pluginURL'];
        $assetsURL = $callingPluginURL . 'vendor/alanblair/wpeasy-library/assets/';
        wp_register_style( 'wpe-lib-common', $assetsURL . 'css/wpe-lib-common.style.css');
        wp_register_style( 'wpe-lib-admin', $assetsURL . 'css/wpe-lib-admin.style.css', ['wpe-lib-common']);

        wp_register_script('wpe-lib-vendor', $assetsURL . 'js/wpe-lib-vendor.bundle.js', false, true);
        wp_register_script('wpe-lib-common', $assetsURL . 'js/wpe-lib-common.bundle.js', ['wpe-lib-vendor'], false, true);
        wp_register_script('wpe-lib-admin', $assetsURL . 'js/wpe-lib-admin.bundle.js', ['jquery','wpe-lib-common'], false, true);
        wp_enqueue_style('wpe-lib-admin'); //Done here so css is in header.. Will be on all Admin pages though.

        wp_register_script('popper', '//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', ['jquery'], false, true);
        wp_enqueue_script('bootstrap', '//stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', ['popper'], false, true );

    }

    static function wp_enqueue_scripts()
    {
        $callingPluginURL = self::$firstCallingPluginConf['pluginURL'];
        $assetsURL = $callingPluginURL . 'vendor/alanblair/wpeasy-library/assets/';
        wp_register_style( 'wpe-lib-common', $assetsURL . 'css/wpe-lib-common.style.css');
        wp_register_style( 'wpe-lib-frontend', $assetsURL . 'css/wpe-lib-frontend.style.css', ['wpe-lib-common']);
        wp_enqueue_style('wpe-lib-frontend');

        wp_register_script('wpe-lib-vendor', $assetsURL . 'js/wpe-lib-vendor.bundle.js', ['jquery'], false,true);
        wp_register_script('wpe-lib-common', $assetsURL . 'js/wpe-lib-common.bundle.js', ['wpe-lib-vendor'], false, true);
        wp_register_script('wpe-lib-frontend', $assetsURL . 'js/wpe-lib-frontend.bundle.js', ['jquery','wpe-lib-common'], false, true);
        wp_enqueue_script('wpe-lib-frontend');
    }

    /**
     * Add the top level menu
     * Note: Adding submenus should use a priority of 11 to work properly.
     */
    static function adminMenuTop()
    {
        global $admin_page_hooks;
        if (!empty($admin_page_hooks[self::MENU_PAGE_SLUG])) return;

        add_menu_page(
            self::MENU_PAGE_TITLE,
            self::MENU_PAGE_NAME,
            self::MENU_PAGE_PERMISSIONS,
            self::MENU_PAGE_SLUG,
            [__CLASS__, 'menuPageOutput'],
            self::WPEASY_EXTERNAL_URL . 'img/trans-wpeasy-icon.png'
        );
    }

    /**
     * Print out a basic page for the root menu
     */
    static function menuPageOutput()
    {
        /*
         * Enqueued here because only needed if the admin page is displayed.
         */
        wp_enqueue_script( 'wpe-lib-admin');
        wp_enqueue_style('wpe-lib-admin');

        ViewHelper::getView(__DIR__. '/View/commonMenuView.phtml',
	        [
		        'wpeExtUrl' => self::WPEASY_EXTERNAL_URL,
	        	'plugins' => self::$loadedPlugins
	        ],
	        true);
    }
}