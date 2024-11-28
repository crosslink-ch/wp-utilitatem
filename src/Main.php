<?php
declare(strict_types=1);

namespace WpUtm;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

use WpUtm\Interfaces\IDynamicCss;
use WpUtm\Interfaces\IDynamicJs;

class Main {

	private ?ContainerInterface $container = null;

	/**
	 * @param $args {
	 *    @type array $definitions Container definitions.
	 * }
	 */
	public function __construct( array $args ) {
		$_args = \wp_parse_args(
			$args,
			array(
				'definitions' => array(),
			)
		);

		$this->get_container( $_args['definitions'] );
	}

	public function make( string $thing, array $args ) {
		if ( ! empty( $args ) ) {
			return $this->container->make( $thing, $args );
		}

		return $this->container->make( $thing );
	}

	public function get( string $thing ) {
		return $this->container->get( $thing );
	}

	public function get_container( array $definitions = array() ): ContainerInterface {
		if ( $this->container ) {
			return $this->container;
		}

		$definitions = \wp_parse_args(
			$definitions,
			array(
				'footer_scripts' => array(),
			)
		);

		$definitions[ AssetsRegistration::class ] = function ( ContainerInterface $c ) {
			return new AssetsRegistration(
				$c->get( IDynamicCss::class ),
				$c->get( IDynamicJs::class ),
				$c->get( Util::class ),
				$c->get( 'prefix' ),
				$c->get( 'footer_scripts' )
			);
		};

		$definitions[ Util::class ] = function ( ContainerInterface $c ) {
			return new Util(
				$c->get( 'main_file' ),
				$c->get( 'type' )
			);
		};

		$builder = new ContainerBuilder();
		$builder->useAttributes( true );
		$builder->addDefinitions( $definitions );
		$this->container = $builder->build();
		return $this->container;
	}
}
