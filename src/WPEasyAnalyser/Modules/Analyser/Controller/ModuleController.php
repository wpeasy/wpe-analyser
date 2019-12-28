<?php


namespace WPEasyAnalyser\Modules\Analyser\Controller;

use Entrecore\GTMetrixClient\GTMetrixClient;
use Entrecore\GTMetrixClient\GTMetrixTest;
use Pdp\Cache;
use Pdp\CurlHttpClient;
use Pdp\Manager;
use WPEasyAnalyser\Application\Controller\ApplicationController;
use WPEasyAnalyser\Application\Model\ApplicationModel;
use WPEasyLibrary\Helpers\View\ViewHelper;
use WPEasyLibrary\Interfaces\IWordPressModule;

class ModuleController implements IWordPressModule {

	static $moduleConfig;

	static $nonceAction = 'wpe_analyser_get';
	static $ajaxNonce;

	static $_init;

	/********************************
	 * Interface Methods
	 */
	static function init() {

		if ( self::$_init ) {
			return;
		}
		self::$_init = true;

		// TODO: Implement init() method.
		self::$moduleConfig = require_once dirname( __DIR__ ) . '/config.php';
		ApplicationController::registerModule( __CLASS__, self::$moduleConfig );
		SettingsController::init( self::$moduleConfig );

		add_action( 'admin_init', [ __CLASS__, 'adminInit' ] );

		add_action( 'wp_ajax_wpe_analyser_get_site_status', [ __CLASS__, 'analyser_get_site_status' ] );
		add_action( 'wp_ajax_wpe_analyser_get_themes', [ __CLASS__, 'analyser_get_themes' ] );
		add_action( 'wp_ajax_wpe_analyser_get_plugins', [ __CLASS__, 'analyser_get_plugins' ] );
		add_action( 'wp_ajax_wpe_analyser_get_gtmetrix', [ __CLASS__, 'analyser_get_gtmetrix' ] );
		add_action( 'wp_ajax_wpe_analyser_get_domain', [ __CLASS__, 'analyser_get_domain' ] );

		add_action( 'wp_ajax_wpe_analyser_save_report', [ __CLASS__, 'analyser_save_report' ] );
		add_action( 'wp_ajax_wpe_analyser_get_saved_reports', [ __CLASS__, 'analyser_get_saved_reports' ] );


	}

	static function adminInit() {
		self::$ajaxNonce = wp_create_nonce( self::$nonceAction );
	}

	static function getDashboardView() {
		return ViewHelper::getView( dirname( __DIR__ ) . '/View/dashboardView.phtml' );
	}

	static function activate() {
		// TODO: Implement activate() method.
	}

	static function upgrade() {
		// TODO: Implement upgrade() method.
	}

	static function getDescription() {
		// TODO: Implement getDescription() method.
	}

	static function deactivate() {
		// TODO: Implement deactivate() method.
	}

	/*************************
	 * AJAX methods
	 */
	static function analyser_get_site_status() {
		if ( ! wp_verify_nonce( $_REQUEST['nonce'], self::$nonceAction ) ) {
			status_header( 401 );
			die( "Not Authorized" );
		}

		die( ViewHelper::getView( dirname( __DIR__ ) . '/View/generalStatusView.phtml' ) );
	}


	static function analyser_get_gtmetrix() {
		set_time_limit( 60 * 3 ); //3 minutes
		if ( ! wp_verify_nonce( $_REQUEST['nonce'], self::$nonceAction ) ) {
			status_header( 401 );
			die( "Not Authorized" );
		}

		$conf = SettingsController::$currentOptions;


		$client = new GTMetrixClient();
		$test   = null;

		try {
			$client->setUsername( $conf['gtmetrix_email'] );
			$client->setAPIKey( $conf['gtmetrix_api_key'] );

			$client->getLocations();
			$client->getBrowsers();
			$test = $client->startTest( site_url() );
		} catch ( \Exception $e ) {
			status_header( 400 );
			die( $e->getMessage() );
		}


		//Wait for result
		while ( $test->getState() != GTMetrixTest::STATE_COMPLETED &&
		        $test->getState() != GTMetrixTest::STATE_ERROR ) {
			$client->getTestStatus( $test );
			sleep( 5 );
		}

		die( ViewHelper::getView(
			dirname( __DIR__ ) . '/View/gtMetrixStatusView.phtml',
			[
				'pageSize'       => \ByteUnits\bytes( $test->getPageBytes() )->format( 'MB' ),
				'pageLoadTime'   => number_format( $test->getPageLoadTime() / 1000, 2 ) . ' sec',
				'pageSpeedScore' => $test->getPagespeedScore() . '%',
				'reportURL'      => $test->getReportUrl()
			]
		) );
	}

	static function analyser_get_plugins() {
		if ( ! wp_verify_nonce( $_REQUEST['nonce'], self::$nonceAction ) ) {
			status_header( 401 );
			die( "Not Authorized" );
		}

		die( ViewHelper::getView( dirname( __DIR__ ) . '/View/pluginStatusView.phtml' ) );
	}


	static function analyser_get_themes() {
		if ( ! wp_verify_nonce( $_REQUEST['nonce'], self::$nonceAction ) ) {
			status_header( 401 );
			die( "Not Authorized" );
		}

		die( ViewHelper::getView( dirname( __DIR__ ) . '/View/themeStatusView.phtml' ) );
	}


	/**
	 * NOTE: Does not work with subdomains.
	 */

	static function analyser_get_domain() {
		if ( ! wp_verify_nonce( $_REQUEST['nonce'], self::$nonceAction ) ) {
			status_header( 401 );
			die( "Not Authorized" );
		}

		if ( version_compare( phpversion(), '7.0' ) === - 1 ) {
			status_header( 400 );
			die( "Server PHP version must be greater than 7.0 to use this function" );
		}

		$apiKey = SettingsController::$currentOptions['securitytrails_api_key'];
		if ( empty( $apiKey ) ) {
			status_header( 403 );
			die( "Invalid Security Trails API Key" );
		}

		$manager = new Manager( new Cache(), new CurlHttpClient() );
		$rules   = $manager->getRules(); //$rules is a Pdp\Rules object
		$host    = $rules->resolve( $_SERVER['HTTP_HOST'] );
		$domain  = $host->getRegistrableDomain();

		$curl = curl_init();

		curl_setopt_array( $curl, array(
			CURLOPT_URL            => "https://api.securitytrails.com/v1/domain/" . $domain,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => "GET",
			CURLOPT_HTTPHEADER     => array(
				"Accept: */*",
				"Accept-Encoding: gzip, deflate",
				"Cache-Control: no-cache",
				"Connection: keep-alive",
				"Content-Type: application/json",
				"apikey: Tm86oYfkuYkGuwAZMTwSUy7o3wXSmu7L",
				"cache-control: no-cache"
			),
		) );

		$response = curl_exec( $curl );
		$err      = curl_error( $curl );

		curl_close( $curl );

		if ( $err ) {
			status_header( 400 );
			die( "cURL Error #:" . $err );
		} else {
			$jsonResponse = json_decode( $response );
			die( ViewHelper::getView( dirname( __DIR__ ) . '/View/domainStatusView.phtml', [ 'data'   => $jsonResponse,
			                                                                                 'domain' => $domain
			] ) );
		}

	}

	static function analyser_save_report() {
		if ( ! wp_verify_nonce( $_REQUEST['nonce'], self::$nonceAction ) ) {
			status_header( 401 );
			die( "Not Authorized" );
		}

		$wpUploadsDir = wp_upload_dir();
		$uploadURL    = $wpUploadsDir['baseurl'] . '/wpe-reports/';
		$uploadPath   = $wpUploadsDir['basedir'] . '/wpe-reports/';

		if ( ! is_dir( $uploadPath ) ) {
			if ( false === mkdir( $uploadPath ) ) {
				die( 'Error creating: ' . $uploadPath );
			}
			file_put_contents( $uploadPath . 'index.php', '' ); //prevent indexing
		}

		$reportName = uniqid( 'report-' ) . '.html';
		$file       = $uploadPath . $reportName;
		$html       = stripslashes( $_REQUEST['data'] );

		$reportHtml = ViewHelper::getView(
			dirname( __DIR__ ) . '/View/templateSavedReport.phtml',
			[
				'assetsUrl' => ApplicationModel::$config['assetsURL'],
				'reportName' => 'Site Report: ' . site_url(),
				'htmlContent' => $html,
				'dateTime' => date('d/m/Y H:i a')
			]
		);

		if ( false === file_put_contents( $file, $reportHtml ) ) {
			status_header( 400 );
			die( 'Could not write to ' . $file );
		}
		$reportURL = $uploadURL . $reportName;
		die( 'Saved report (' . $reportName . ') <a class="btn btn-primary btn-sm" target="_blank" href="' . $reportURL . '">Open</a>' );
	}


	static function analyser_get_saved_reports() {
		if ( ! wp_verify_nonce( $_REQUEST['nonce'], self::$nonceAction ) ) {
			status_header( 401 );
			die( "Not Authorized" );
		}

		$wpUploadsDir = wp_upload_dir();
		$uploadURL    = $wpUploadsDir['baseurl'] . '/wpe-reports/';
		$uploadPath   = $wpUploadsDir['basedir'] . '/wpe-reports/';

		$files = array_splice( scandir( $uploadPath ), 2 ); //removes . and ..

		$fileArray = [];
		foreach ( $files as $file ) {
			if($file === 'index.php') continue;

			$time        = filectime( $uploadPath . $file );
			$fileArray[$time] =
				[
					'name' => $file,
					'url'  => $uploadURL . $file
				];
		}
		krsort( $fileArray );

		die( ViewHelper::getView(
			dirname( __DIR__ ) . '/View/savedReportsListView.phtml',
			[
				'reports' => $fileArray
			]
		) );
	}
}