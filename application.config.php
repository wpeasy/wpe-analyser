<?php
$pluginUrl = plugin_dir_url(__FILE__);

return [
	'isDebug' => defined('WP_DEBUG' && WP_DEBUG === true),
	'pluginName' => 'WPEasy Analyser',
	'pluginDescription' => 'WPEasy Analyser Tools',
	'moduleDir' => __DIR__ . '/src/WPEasyAnalyser/Modules/',
	'pluginURL' => $pluginUrl,
	'assetsURL' => $pluginUrl . 'assets/',
	//Modules Controllers to initialise
	'pluginController' => '\WPEasyAnalyser\Application\Controller\ApplicationController',
	'modules' => [
		'\WPEasyAnalyser\Modules\Analyser\Controller\ModuleController'
	],

	'adminMenu' =>
		[
			'slug' => 'wpe_analyser',
			'pageTitle' => 'WP Easy Analyser',
			'menuTitle' => 'WP Easy Analyser',
			'capability' => 'manage_options'
		]
];
