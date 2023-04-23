<?php

namespace WpUtm;

use WpUtm\Interfaces\IDynamicCss;
use WpUtm\Interfaces\IDynamicJs;

class AssetsRegistration {
	public function register_assets() {
		\add_action( 'wp_loaded', array( $this, 'register_scripts' ) );
		\add_action( 'wp_loaded', array( $this, 'register_styles' ) );
		\add_action( 'enqueue_block_assets', fn() => $this->enqueue_block_assets( \get_the_ID() ) );
		\add_action( 'enqueue_block_editor_assets', fn() => $this->enqueue_block_assets( \get_the_ID() ) );
		\add_action( 'enqueue_block_assets', fn() => $this->add_inline_assets( 'style' ), 100 );
		\add_action( 'enqueue_block_assets', fn() => $this->add_inline_assets( 'script' ), 100 );
		\add_action( 'enqueue_block_editor_assets', fn() => $this->add_inline_assets( 'style' ), 100 );
		\add_action( 'enqueue_block_editor_assets', fn() => $this->add_inline_assets( 'script' ), 100 );
	}

	public function __construct(
		public IDynamicCss $css,
		public IDynamicJs $js,
		public Util $util,
		public string $prefix,
		public array $footer_scripts
	) {}

	public function enqueue_block_assets( $post_id ) {
		if ( empty( $post_id ) ) {
			return;
		}
		$post   = get_post( $post_id );
		$blocks = \parse_blocks( $post->post_content );
		if ( ! empty( $blocks ) && ! empty( $blocks[0]['blockName'] ) ) {
			$this->enqueue_assets_for_blocks( $blocks );
		}
	}

	public function enqueue_assets_for_blocks( array $blocks ) {
		foreach ( $blocks as $block ) {
			$block_name = str_replace( '/', '-', $block['blockName'] );

			// Handle reusable blocks
			if ( $block_name === 'core-block' ) {
				$post_id = $block['attrs']['ref'];
				$this->enqueue_block_assets( $post_id );
				continue;
			}

			$handle     = $this->prefix . '-' . $block_name;

			if ( ! \wp_script_is( $handle ) ) {
				$data = $this->get_script_data( $block_name );
				if ( ! empty( $data['version'] ) ) {
					$file = $block_name . '.bundle.js';
					\wp_enqueue_script( "{$handle}", $this->util->get_asset_url( "build/js/{$file}" ), $data['dependencies'], $data['version'] );
				}
			}

			if ( ! \wp_style_is( $handle ) ) {
				$css_file = $this->util->get_asset_abs_path( "build/css/{$block_name}.css" );
				if ( \file_exists( $css_file ) ) {
					\wp_enqueue_style( $handle, $this->util->get_asset_url( "build/css/{$block_name}.css" ), array(), \filemtime( $css_file ) );
				}
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$this->enqueue_assets_for_blocks( $block['innerBlocks'] );
			}
		}
	}

	public function register_scripts() {
		$scripts_path = $this->util->get_asset_abs_path( 'build/js' );

		if ( ! file_exists( $scripts_path ) ) {
			return;
		}

		$scripts_files = scandir( $scripts_path );

		foreach ( $scripts_files as $file ) {

			if ( preg_match( '/(?<name>.+?)\\.bundle\\.js$/', $file, $matches ) ) {

				$name = $matches['name'];
				$data = $this->get_script_data( $name );

				$is_footer_script = \in_array( $name, $this->footer_scripts, true );

				\wp_register_script(
					"{$this->prefix}-{$name}",
					$this->util->get_asset_url( "build/js/{$file}" ),
					$data['dependencies'],
					$data['version'],
					$is_footer_script
				);

			}
		}
	}

	public function add_inline_assets( string $type = 'script' ) {
		if ( $type === 'script' ) {
			global $wp_scripts;
			$container = $wp_scripts;
			$jc        = new \ReflectionClass( $this->js );
		} else {
			global $wp_styles;
			$container = $wp_styles;
			$jc        = new \ReflectionClass( $this->css );
		}

		$jc_methods = $jc->getMethods();

		foreach ( $jc_methods as $method ) {

			$attributes = $method->getAttributes( 'WpUtm\\Attributes\\InlineAsset' );

			foreach ( $attributes as $attribute ) {
				$ai = $attribute->newInstance();

				// This hook is not enqueued. Skip.
				if ( ! in_array( $ai->hook, $container->queue, true ) ) {
					continue;
				}

				if ( $type === 'script' ) {
					\wp_add_inline_script(
						$ai->hook,
						$this->js->{$method->getName()}(),
						'before'
					);
				} else {
					\wp_add_inline_style(
						$ai->hook,
						$this->css->{$method->getName()}()
					);
				}
			}
		}
	}

	public function register_styles() {
		$register_styles_cb = function( $styles_files, $prefix, $base_path, $inject = false ) {
			foreach ( $styles_files as $file ) {
				if ( \preg_match( '/(?<name>.+?)\\.css$/', $file, $matches ) ) {
					$name = $matches['name'];
					if ( $inject ) {
						$contents = \file_get_contents( $this->util->get_asset_abs_path( $base_path . $file ) );
						\add_action( 'wp_footer', function() use ($contents) {
							echo '<style>';
							echo $contents;
							echo '</style>';
						} );
					} else {
						\wp_register_style(
							"{$prefix}-{$name}",
							$this->util->get_asset_url( $base_path . $file ),
							array(),
							\filemtime( $this->util->get_asset_abs_path( $base_path . $file ) )
						);
					}
				}
			}
		};

		$styles_path = $this->util->get_asset_abs_path( 'build/css/' );

		if ( file_exists( $styles_path ) ) {
			$styles_files = scandir( $styles_path );
			\call_user_func( $register_styles_cb, $styles_files, $this->prefix, 'build/css/' );
		}

		// CSS files generated by webpack
		$js_styles_path = $this->util->get_asset_abs_path( 'build/js/' );

		if ( file_exists( $js_styles_path ) ) {
			$styles_files = scandir( $js_styles_path );
			\call_user_func( $register_styles_cb, $styles_files, "{$this->prefix}-extracted-css", 'build/js/', true );
		}

	}

	/**
	 * Get script data as produced by dependency extraction webpack plugin
	 *
	 * @param string $script_name Script name defined by a webpack entry point.
	 * @return array Script data (version, dependencies)
	 */
	public function get_script_data( $script_name ) {
		$assets_path = $this->util->get_asset_abs_path( 'build/js/' . $script_name . '.bundle.asset.php' );

		if ( file_exists( $assets_path ) ) {
			$data = require $assets_path;
			$data = \apply_filters( "{$this->prefix}_script_data", $data, $script_name );
			return $data;
		}

		return array(
			'dependencies' => array(),
			'version'      => '',
		);
	}
}
