<?php
namespace JMichaelWard\BoardGameCollector;

use Auryn\Injector;
use WebDevStudios\OopsWP\Structure\Plugin\Plugin;
use WebDevStudios\OopsWP\Structure\Service;
use JMichaelWard\BoardGameCollector\Content\ContentRegistrar;
use JMichaelWard\BoardGameCollector\Api\ApiService;
use JMichaelWard\BoardGameCollector\Cli\CliService;
use JMichaelWard\BoardGameCollector\Cron\CronService;
use JMichaelWard\BoardGameCollector\Admin\Settings;
use WebDevStudios\OopsWP\Utility\FilePathDependent;

/**
 * Class BoardGameCollector
 *
 * @package JMichaelWard\BoardGameCollector
 */
class BoardGameCollector extends Plugin {
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
	 * @param string   $file_path Path to the root of this plugin.
	 * @param Injector $injector  Auryn\Injector instance.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-04-12
	 */
	public function __construct( string $file_path, Injector $injector ) {
		$this->file_path = $file_path;
		$this->injector  = $injector;
	}

	/**
	 * Array of instantiated Service objects.
	 *
	 * @var array
	 */
	protected $services = [
		ContentRegistrar::class,
		ApiService::class,
		CliService::class,
		CronService::class,
		Settings::class,
	];

	/**
	 * Kick off the plugin functionality!
	 *
	 * @throws ConfigException If misconfigured.
	 * @since 1.0.0
	 */
	public function run() {
		parent::run();

		// Check to see if it's time to run cron processes.
		CronService::maybe_schedule_cron();
	}

	/**
	 * Register framework services.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-02-21
	 */
	protected function register_services() {
		$objects = array_map(
			function ( $object_classname ) {
				return [
					'namespace' => $object_classname,
					'object'    => $this->injector->make( $object_classname ),
				];
			},
			$this->services
		);

		$this->services = array_column( $objects, 'object', 'namespace' );

		array_walk( $this->services, [ $this, 'register_service' ] );
	}

	/**
	 * Register a single framework service.
	 *
	 * @param Service $service Service class.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-02-21
	 */
	protected function register_service( Service $service ) {
		if ( in_array( FilePathDependent::class, class_uses( $service ), true ) ) {
			/* @var $service \WebDevStudios\OopsWP\Utility\FilePathDependent Path-dependent service. */
			$service->set_file_path( $this->file_path );
		}

		$service->run();
	}
}
