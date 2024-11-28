<?php

namespace WpUtm;

use WP_Theme;
use Exception;

class Util {

	private ?WP_Theme $theme = null;
	private ?string $dirname = null;

	public function __construct(
		public string $main_file,
		public string $type
	) {}

	public function get_asset_url( string $path ): string {
		$path = ltrim( $path, '/' );

		if ( $this->type === 'theme' ) {
			if ( $this->is_active_theme() ) {
				return get_stylesheet_directory_uri() . '/' . $path;
			} elseif ( $this->is_parent_theme() ) {
				return get_template_directory_uri() . '/' . $path;
			} else {
				throw new Exception( 'Must be active or parent theme.' );
			}
		}

		return plugins_url( $path, $this->main_file );
	}

	public function get_asset_abs_path( string $path ): string {
		$path = ltrim( $path, '/' );

		if ( $this->type === 'theme' ) {
			if ( $this->is_active_theme() ) {
				return get_stylesheet_directory() . '/' . $path;
			} elseif ( $this->is_parent_theme() ) {
				return get_template_directory() . '/' . $path;
			} else {
				throw new Exception( 'Must be active or parent theme.' );
			}
		}

		return plugin_dir_path( $this->main_file ) . $path;
	}

	public function get_theme(): WP_Theme {
		if ( $this->theme ) {
			return $this->theme;
		}

		$this->theme = wp_get_theme();

		return $this->theme;
	}

	public function get_dirname(): string {
		if ( $this->dirname ) {
			return $this->dirname;
		}

		$this->dirname = basename( dirname( $this->main_file ) );

		return $this->dirname;
	}

	public function is_parent_theme(): bool {
		$theme = $this->get_theme();

		return $theme->get_template() !== $theme->get_stylesheet() && $this->get_dirname() === $theme->get_template();
	}

	public function is_active_theme(): bool {
		$theme = $this->get_theme();

		return $this->get_dirname() === $theme->get_stylesheet();
	}
}
