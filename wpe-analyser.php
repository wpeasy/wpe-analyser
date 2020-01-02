<?php
/**
 * Plugin Name: WPEasy Analyser
 * Plugin URI:
 * Description: Tool to do analysis of a WordPress site status
 * Version: 1.0.7
 * Author: Alan Blair
 * Author URI:
 * Text Domain: wpeasy
 */

namespace WPEasyAnalyserPlugin;

use WPEasyLibrary\WordPress\UpdateFromGithubController;
use WPEasyLibrary\WordPress\WPEasyApplication;

require_once __DIR__ . '/vendor/autoload.php';

if ( is_admin() ) {
    new UpdateFromGithubController( __FILE__, 'wpeasy', "wpe-analyser" );

}

$config = require __DIR__ . '/application.config.php';

WPEasyApplication::init($config);
WPEasyApplication::registerLoadedPlugin($config);

