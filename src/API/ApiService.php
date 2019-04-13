<?php
namespace JMichaelWard\BoardGameCollector\Api;

use JMichaelWard\BoardGameCollector\API\Routes\Games;
use WebDevStudios\OopsWP\Structure\Service;

/**
 * Class ApiService
 *
 * @package JMichaelWard\BoardGameCollector\API
 */
class ApiService extends Service {
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
	public function register_hooks() {
		/* @var \WP_REST_Controller $route REST route controller */
		foreach ( $this->routes as $route ) {
			add_action( 'rest_api_init', [ new $route(), 'register_api_fields' ] );
		}
	}
}
