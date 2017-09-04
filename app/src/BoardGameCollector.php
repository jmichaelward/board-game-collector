<?php
namespace JMichaelWard\BoardGameCollector;

use JMichaelWard\BoardGameCollector\Service\Settings;
use JMichaelWard\BoardGameCollector\Service\Content;
use JMichaelWard\BoardGameCollector\Service\API;
use JMichaelWard\BoardGameCollector\Service\Cron;
use JMichaelWard\BoardGameCollector\Updater\GamesUpdater;

/**
 * Class BoardGameCollector
 *
 * @package JMichaelWard\BoardGameCollector
 */
class BoardGameCollector implements Hookable {
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
		return plugin_dir_path( dirname( __FILE__ ) );
	}

	/**
	 * Implementation of Hookable method.
	 */
	public function hooks() {
		add_action( 'plugins_loaded', [ $this, 'register_services' ] );
	}

	/**
	 * Array of Service classes for this plugin.
	 *
	 * @return array
	 */
	public function get_services() {
		return [
			Settings::class,
			API::class,
			Content::class,
			Cron::class,
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
		if ( Cron::class === $service ) {
			$this->services[ $service ] = new $service( new GamesUpdater( $this->services[ Settings::class ] ) );
			return $this->services[ $service ];
		}

		$this->services[ $service ] = new $service;
		return $this->services[ $service ];
	}

	/**
	 * Call hooks methods on all registered Service objects.
	 */
	public function register_services() {
		$services = array_map( [ $this, 'instantiate_services' ], $this->get_services() );

		array_walk( $services, function( Service $service ) {
			$service->hooks();
		});
	}

	/**
	 * Kick things off.
	 */
	public function run() {
		// Check to see if it's time to run cron processes.
		Cron::maybe_schedule_cron();

		$this->hooks();
	}
}
