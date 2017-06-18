<?php
namespace JMichaelWard\BoardGameCollector\API;

use JMichaelWard\BoardGameCollector\API\Routes\Games;
use JMichaelWard\BoardGameCollector\WP\EventInterface;

/**
 * Class Registrar
 *
 * @package JMichaelWard\BoardGameCollector\API
 */
class Registrar implements EventInterface {
	/**
	 * Collection of routes for this plugin.
	 *
	 * @var array
	 */
	private $routes = [
		Games::class,
	];

	/**
	 * Register WordPress hooks.
	 */
	public function hooks() {
		/* @var \WP_REST_Controller $route REST route controller */
		foreach ( $this->routes as $route ) {
			add_action( 'rest_api_init', [ new $route, 'register_routes' ] );
		}
	}
}
