<?php
namespace JMichaelWard\BoardGameCollector;

use JMichaelWard\BoardGameCollector\Service as Service;
use JMichaelWard\BoardGameCollector\Updater\GamesUpdater;

/**
 * Class BoardGameCollector
 *
 * @package JMichaelWard\BoardGameCollector
 */
class BoardGameCollector {
	/**
	 * Array of instantiated Service objects.
	 *
	 * @var array
	 */
	private $services;

	/**
	 * Path to the application root.
	 *
	 * @return string
	 */
	public static function app_path() {
		return dirname( plugin_dir_path( __FILE__ ) ) . '/app/';
	}

	/**
	 * Set up the plugin and run.
	 */
	public function run() {
		// Check to see if it's time to run cron processes.
		Service\Cron::maybe_schedule_cron();

		$this->register_services();
	}

	/**
	 * Array of Service classes for this plugin.
	 *
	 * @return array
	 */
	public function get_services() {
		return [
			Service\Settings::class,
			Service\API::class,
			Service\Content::class,
			Service\Cron::class,
		];
	}

	/**
	 * Create instantiated instances of Service objects.
	 *
	 * @param string $service The fully-qualified class namespace of the Service to instantiate.
	 *
	 * @return Service
	 */
	private function instantiate_services( $service ) {
		if ( Service\Cron::class === $service ) {
			$this->services[ $service ] = new $service( new GamesUpdater( $this->services[ Service\Settings::class ] ) );
			return $this->services[ $service ];
		}

		$this->services[ $service ] = new $service();

		return $this->services[ $service ];
	}

	/**
	 * Call hooks methods on all registered Service objects.
	 */
	public function register_services() {
		$services = array_map( [ $this, 'instantiate_services' ], $this->get_services() );

		array_walk( $services, function( Service\Service $service ) {
			$service->register_hooks();
		});
	}
}