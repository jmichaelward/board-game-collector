<?php
namespace JMichaelWard\BoardGameCollector;

use Auryn\ConfigException;
use Auryn\Injector;
use JMichaelWard\BoardGameCollector\Updater\ImageProcessor;
use JMichaelWard\BoardGameCollector\Utility\InstantiatorInterface;
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
		ImageProcessor::class,
	];

	/**
	 * Collection of services which are sharable by the plugin.
	 *
	 * @var array
	 */
	private $shareable_services = [
		Settings::class,
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
	public function run(): void {
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
	 *
	 * @return array
	 */
	protected function init_services(): array {
		$objects = array_map(
			function ( $service_classname ) {
				try {
					$service = $this->injector->make( $service_classname );

					$this->setup_service( $service );

					return [
						'namespace' => $service_classname,
						'object'    => $service,
					];
				} catch ( \Throwable $e ) {
					$this->services = [];

					return $this->services;
				}
			},
			$this->services
		);

		return array_column( $objects, 'object', 'namespace' );
	}

	/**
	 * Run post-instantiation processes on a given service.
	 *
	 * @param Service $service Service instance.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2020-09-13
	 * @throws ConfigException If Injector misconfiguration exists.
	 * @return void
	 */
	private function setup_service( Service $service ): void {
		$service_class = get_class( $service );

		if ( in_array( InstantiatorInterface::class, class_implements( $service_class ), true ) ) {
			/* @var InstantiatorInterface $service InstantiatorInterface service. */
			$service->set_injector( $this->injector );
		}

		if ( in_array( Hydratable::class, class_implements( $service_class ), true ) ) {
			/* @var Hydratable $service Hydratable service. */
			$service->hydrate();
		}

		if ( $this->is_shared_service( $service ) ) {
			$this->share_service( $service );
		}
	}

	/**
	 * Sets up the service for sharing.
	 *
	 * @param Service $service Service instance.
	 *
	 * @throws ConfigException If there's a problem finding the Service within Auryn.
	 */
	private function share_service( Service $service ): void {
		$this->injector->share( $service );
	}

	/**
	 * Check whether the service is shared.
	 *
	 * @param Service $service Service instance.
	 *
	 * @return bool
	 */
	private function is_shared_service( Service $service ): bool {
		return in_array( get_class( $service ), $this->shareable_services, true );
	}
}
