<?php
namespace JMichaelWard\BoardGameCollector\Api;

use JMichaelWard\BoardGameCollector\Api\Routes\Collection;
use JMichaelWard\BoardGameCollector\Api\Routes\Games;
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
		Collection::class,
	];

	/**
	 * Register WordPress hooks.
	 */
	public function register_hooks() {
		add_action( 'rest_api_init', [ $this, 'register_extended_api_fields' ] );
	}

	/**
	 * Extend default WordPress API results with extra fields.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-05-01
	 * @return void
	 */
	public function register_extended_api_fields() {
		foreach ( $this->routes as $route_class ) {
			$route = new $route_class();
			$route->register();
		}
	}
}
