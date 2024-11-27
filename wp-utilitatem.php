<?php
/**
 * Plugin Name: WP Utilitatem
 * Description: Plugin for testing utilities. Do not use as plugin.
 * Requires at least: 6.1
 * Requires PHP: 8.1
 * Version: 0.1.0
 * Author: Cross-Link
 * Text Domain: wputm
 */

require 'vendor/autoload.php';

global $wputm;

function wputm_init() {
	global $wputm;

	$wputm = new \WpUtm\Main(
		array(
			'definitions' => array(
				\WpUtm\Interfaces\IDynamicCss::class => \DI\create( \WpUtm\Plugin\DynamicCss::class ),
				\WpUtm\Interfaces\IDynamicJs::class  => \DI\create( \WpUtm\Plugin\DynamicJs::class ),
				'main_file'                          => __FILE__,
				'type'                               => 'plugin', // set theme or plugin here
				'prefix'                             => 'wputm',
				'footer_scripts'                     => array( 'footer-script' ),
			),
		)
	);

	$wputm->get( \WpUtm\Plugin\Main::class )->init();
}

wputm_init();
