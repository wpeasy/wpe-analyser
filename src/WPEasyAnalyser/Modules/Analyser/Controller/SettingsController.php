<?php


namespace WPEasyAnalyser\Modules\Analyser\Controller;


class SettingsController {
	private static $_init;

	static $settingsConfig;

	static $currentOptions;

	static $nonceAction = 'wpe_analyser';

	static function init( $conf )
	{
		if (self::$_init) return;
		self::$_init = true;

		$settingsConfig  = $conf['settings'];
		self::$settingsConfig = $settingsConfig;
		self::$currentOptions = get_option($settingsConfig ['optionName']);

		add_action('wp_ajax_wpe_analyser_save', [__CLASS__, 'saveSettings']);
	}

	static function saveSettings()
	{
		if ( !wp_verify_nonce( $_REQUEST['nonce'], self::$nonceAction)) {
			exit("Warning: No access");
		}

		$optionName = self::$settingsConfig['optionName'];
		$str = parse_str(urldecode($_POST['data']), $arr);
		update_option($optionName, $arr[$optionName]);
		die();
	}
}