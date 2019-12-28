<?php


namespace WPEasyAnalyser\Application\Controller;


use WPEasyAnalyser\Application\Model\ApplicationModel;
use WPEasyLibrary\Helpers\View\ViewHelper;
use WPEasyLibrary\WordPress\WPEasyApplication;

class ApplicationController {
	private static $_init;

	static function init($config) {
		if ( self::$_init ) {
			return;
		}
		self::$_init  = true;
		ApplicationModel::$config = $config;


		add_action( 'admin_menu', [ __CLASS__, 'admin_menu' ] );
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'wp_enqueue_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'admin_enqueue_scripts' ] );
	}

	static function wp_enqueue_scripts()
	{
		$assetsURL = ApplicationModel::$config['assetsURL'];
		self::_common_enqueue_scripts();
		wp_enqueue_script('wpe-analyser-frontend', $assetsURL . 'js/wpe-analyser-frontend.bundle.js', ['wpe-analyser-common'], false, true);
	}

	static function admin_enqueue_scripts()
	{
		$assetsURL = ApplicationModel::$config['assetsURL'];
		self::_common_enqueue_scripts();
		wp_enqueue_script('wpe-analyser-admin', $assetsURL . 'js/wpe-analyser-admin.bundle.js', ['wpe-analyser-common'], false, true);
	}

	private static function _common_enqueue_scripts()
	{
		$assetsURL = ApplicationModel::$config['assetsURL'];
		wp_register_script('wpe-analyser-vendor', $assetsURL . 'js/vendor.bundle.js' );
		wp_register_script('wpe-analyser-common', $assetsURL . 'js/wpe-analyser-common.bundle.js', ['wpe-analyser-vendor'], false, true );
	}



	static function registerModule($controllerClass, $moduleConfig)
	{
		ApplicationModel::$loadedModules[$controllerClass] = $moduleConfig;
	}

	static function admin_menu()
	{
		$config = ApplicationModel::$config['adminMenu'];
		add_submenu_page(
			WPEasyApplication::MENU_PAGE_SLUG,
			$config['pageTitle'],
			$config['menuTitle'],
			$config['capability'],
			$config['slug'],
			[__CLASS__, 'admin_menu_callback']
		);
	}

	static function admin_menu_callback()
	{
		wp_enqueue_script( 'wpe-lib-admin');
		//wp_enqueue_style('wpe-lib-admin'); //Do from library so is always in teh header. This hook gets called too late.

		ViewHelper::getView(dirname(__DIR__) . '/View/applicationView.phtml',[], true);

	}
}