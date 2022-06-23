<?php
/**
 * Service to register custom API endpoints with WordPress.
 *
 * @package JMichaelWard\BoardGameCollector\Api
 */

namespace JMichaelWard\BoardGameCollector\Api;

use JMichaelWard\BoardGameCollector\Admin\Settings;
use JMichaelWard\BoardGameCollector\Model\Games\BggGameAdapter;
use JMichaelWard\BoardGameCollector\Utility\FactoryService;
use WebDevStudios\OopsWP\Structure\Service;
use JMichaelWard\BoardGameCollector\Api\Routes\WPExtension;
use JMichaelWard\BoardGameCollector\Api\Routes\Custom;

/**
 * Class ApiService
 *
 * @package JMichaelWard\BoardGameCollector\API
 */
class ApiService extends FactoryService {
	/**
	 * Collection of routes for this plugin.
	 *
	 * @var array
	 */
	private $routes = [
		WPExtension\Games::class,
		Custom\Collection::class,
	];

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'rest_api_init', [ $this, 'register_extended_api_fields' ] );
	}

	/**
	 * Extend default WordPress API results with extra fields.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-05-01
	 * @return void
	 */
	public function register_extended_api_fields(): void {
		foreach ( $this->routes as $route_class ) {
			$this->create( $route_class )->register();
		}
	}
}
