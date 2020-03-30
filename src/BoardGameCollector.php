<?php
namespace JMichaelWard\BoardGameCollector;

use Auryn\ConfigException;
use Auryn\Injector;
use JMichaelWard\OopsWPPlus\Utility\Hydratable;
use WebDevStudios\OopsWP\Structure\Plugin\Plugin;
use JMichaelWard\BoardGameCollector\Content\ContentRegistrar;
use JMichaelWard\BoardGameCollector\Api\ApiService;
use JMichaelWard\BoardGameCollector\UI\Cli\CliService;
use JMichaelWard\BoardGameCollector\Cron\CronService;
use JMichaelWard\BoardGameCollector\Admin\Settings;
use WebDevStudios\OopsWP\Structure\Service;

/**
 * Class BoardGameCollector
 *
 * @package JMichaelWard\BoardGameCollector
 */
final class BoardGameCollector extends Plugin {
	/**
	 * Array of instantiated Service objects.
	 *
	 * IMPORTANT: The order of these services matter as of 3/28/20.
	 * ************************************************************
	 * The CliService now has a dependency on the Settings service. In order for Auryn to properly share the
	 * same object with the CliService, it's necessary for us to initialize that class first, and hydrate it
	 * with data from the WordPress settings panel. This will ensure that values such as a person's username
	 * for BoardGameGeek will get included in API requests.
	 *
	 * @see BoardGameCollector::init_services()
	 * @var array
	 */
	protected $services = [
		Settings::class,
		ContentRegistrar::class,
		ApiService::class,
		CliService::class,
		CronService::class,
	];

	/**
	 * Auryn\Injector instance.
	 *
	 * @var Injector
	 * @since 2019-04-12
	 */
	private $injector;

	/**
	 * BoardGameCollector constructor.
	 *
	 * @param string   $file     Plugin bootstrap file.
	 * @param Injector $injector Auryn\Injector instance.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-04-12
	 */
	public function __construct( string $file, Injector $injector ) {
		$this->file_path = $file;
		$this->injector  = $injector;
	}

	/**
	 * Kick off the plugin functionality!
	 *
	 * @since 1.0.0
	 */
	public function run() {
		parent::run();

		// Check to see if it's time to run cron processes.
		CronService::maybe_schedule_cron();
	}

	/**
	 * Register plugins services.
	 *
	 * This method overrides the one defined in OOPS-WP so we can use the Auryn DI container to create our objects.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-02-21
	 */
	protected function init_services() {
		$objects = array_map(
			function ( $service_classname ) {
				$service = $this->injector->make( $service_classname );

				if ( $this->is_hydratable_service( $service_classname ) ) {
					$service->hydrate();
				}

				if ( $this->is_shared_service( $service_classname ) ) {
					$this->share_service( $service );
				}

				return [
					'namespace' => $service_classname,
					'object'    => $service,
				];
			},
			$this->services
		);

		return array_column( $objects, 'object', 'namespace' );
	}

	/**
	 * Sets up the service for sharing.
	 *
	 * @param Service $service Service instance.
	 *
	 * @throws ConfigException If there's a problem finding the Service within Auryn.
	 */
	private function share_service( Service $service ) {
		$this->injector->share( $service );
	}

	/**
	 * Check whether the service is shared.
	 *
	 * @param string $service_classname The fully-qualified class name of the Service.
	 *
	 * @return bool
	 */
	private function is_shared_service( $service_classname ) {
		return in_array(
			$service_classname,
			[
				Settings::class,
			],
			true
		);
	}

	/**
	 * Check whether the Service requires hydration before use.
	 *
	 * @param string $service_classname The fully-qualified class name of the Service.
	 *
	 * @return bool
	 */
	private function is_hydratable_service( $service_classname ) {
		return in_array( Hydratable::class, class_implements( $service_classname ), true );
	}
}
