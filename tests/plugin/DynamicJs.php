<?php
declare(strict_types=1);

namespace WpUtm\Plugin;

defined( 'ABSPATH' ) || exit;

use WpUtm\Attributes\InlineAsset;
use WpUtm\Interfaces\IDynamicJs;

class DynamicJs implements IDynamicJs {

	#[InlineAsset( 'nonexisting-asset-hook' )]
	public function nonexisting_asset_js() {
		\add_option( 'nonexisting_asset_js_ran', 'yes' );
		return <<<JS
console.log('Hello world');
JS;
	}

	#[InlineAsset( 'wputm-main' )]
	public function wputm_main_js() {
		\add_option( 'wputm_main_js_ran', 'yes' );
		return 'console.log("hello")';
	}
}
