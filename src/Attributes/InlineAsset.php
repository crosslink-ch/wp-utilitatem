<?php

namespace WpUtm\Attributes;

defined( 'ABSPATH' ) || exit;

#[\Attribute]
class InlineAsset {

	public function __construct( public ?string $hook = null ) {
		$this->hook = $hook;
	}
}
