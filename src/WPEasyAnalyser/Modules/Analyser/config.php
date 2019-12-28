<?php

/*
 * NOTE.. Attribute names may be in camel case to match a library.
 */
$controllerClass = 'WPEasyAnalyser\Modules\Analyser\Controller\ModuleController';
return [
	'moduleName' => 'WPEasy Analyser',
	'moduleDescription' => "Performs an analysis of the site's Wordpress, Hosting and Performance",
	'path' => __DIR__,
	'url' => plugin_dir_url(__FILE__),
	'settings' =>
		[
			'optionName' => 'wpe_analysers',
			'capability' => 'manage_options'
		],
];