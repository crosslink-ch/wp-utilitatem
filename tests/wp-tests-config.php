<?php

if ( ! function_exists( 'getenv_docker' ) ) {

	// https://github.com/docker-library/wordpress/issues/588 (WP-CLI will load this file 2x)

	function getenv_docker( $env, $default ) {

		if ( $fileEnv = getenv( $env . '_FILE' ) ) {

			return rtrim( file_get_contents( $fileEnv ), "\r\n" );

		} elseif ( ( $val = getenv( $env ) ) !== false ) {

			return $val;

		} else {

			return $default;

		}

	}
}

// define( 'WP_DEFAULT_THEME', 'default' );

/* Path to the WordPress codebase you'd like to test. Add a forward slash in the end. */
define( 'ABSPATH', dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/' );

// Test with multisite enabled.
// Alternatively, use the tests/phpunit/multisite.xml configuration file.
// define( 'WP_TESTS_MULTISITE', true );

// Force known bugs to be run.
// Tests with an associated Trac ticket that is still open are normally skipped.
// define( 'WP_TESTS_FORCE_KNOWN_BUGS', true );

// Test with WordPress debug mode (default).
define( 'WP_DEBUG', true );

// ** MySQL settings ** //

// This configuration file will be used by the copy of WordPress being tested.
// wordpress/wp-config.php will be ignored.

define( 'DB_NAME', getenv_docker( 'WORDPRESS_DB_NAME', 'wordpress' ) );

define( 'DB_USER', getenv_docker( 'WORDPRESS_DB_USER', 'example username' ) );

define( 'DB_PASSWORD', getenv_docker( 'WORDPRESS_DB_PASSWORD', 'example password' ) );

define( 'DB_HOST', getenv_docker( 'WORDPRESS_DB_HOST', 'mysql' ) );

define( 'DB_CHARSET', getenv_docker( 'WORDPRESS_DB_CHARSET', 'utf8' ) );

define( 'DB_COLLATE', getenv_docker( 'WORDPRESS_DB_COLLATE', '' ) );

$table_prefix = getenv_docker( 'WORDPRESS_TABLE_PREFIX', 'wp_' );

/**#@+

 * Authentication unique keys and salts.

 *

 * Change these to different unique phrases! You can generate these using

 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.

 *

 * You can change these at any point in time to invalidate all existing cookies.

 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */

define( 'AUTH_KEY', getenv_docker( 'WORDPRESS_AUTH_KEY', '1b2012e94c9c454de08658d21a3af27a9caae4e3' ) );

define( 'SECURE_AUTH_KEY', getenv_docker( 'WORDPRESS_SECURE_AUTH_KEY', '48aff5650d721d974565bdeff7ea7342e4e9c8aa' ) );

define( 'LOGGED_IN_KEY', getenv_docker( 'WORDPRESS_LOGGED_IN_KEY', 'e6b31bd75e3816fe4111c0258c2009eb631c14f4' ) );

define( 'NONCE_KEY', getenv_docker( 'WORDPRESS_NONCE_KEY', '016e3a689e63c7cb19c2aeed19c22bb6d7d0af94' ) );

define( 'AUTH_SALT', getenv_docker( 'WORDPRESS_AUTH_SALT', 'afcdd77c9978f0610f04a64a46f28524174a33c5' ) );

define( 'SECURE_AUTH_SALT', getenv_docker( 'WORDPRESS_SECURE_AUTH_SALT', '3fa7a2bd2554f6126a1fa01cb0219cd47ee3b671' ) );

define( 'LOGGED_IN_SALT', getenv_docker( 'WORDPRESS_LOGGED_IN_SALT', 'f30723150886b764a0d340f3d65106c34e973be2' ) );

define( 'NONCE_SALT', getenv_docker( 'WORDPRESS_NONCE_SALT', '785ee3e958a5381492ee0a34ae5d98d21930d291' ) );

// (See also https://wordpress.stackexchange.com/a/152905/199287)

define( 'WP_TESTS_DOMAIN', 'localhost:8889' );
define( 'WP_TESTS_EMAIL', 'admin@wptest.local' );
define( 'WP_TESTS_TITLE', 'WP Utilitatem' );

define( 'WP_PHP_BINARY', 'php' );

define( 'WPLANG', '' );
