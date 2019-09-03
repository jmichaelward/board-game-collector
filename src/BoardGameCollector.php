<?php
namespace JMichaelWard\BoardGameCollector;

use Auryn\Injector;
use WebDevStudios\OopsWP\Structure\Plugin\Plugin;
use WebDevStudios\OopsWP\Structure\Service;
use JMichaelWard\BoardGameCollector\Content\ContentRegistrar;
use JMichaelWard\BoardGameCollector\Api\ApiService;
use JMichaelWard\BoardGameCollector\UI\Cli\CliService;
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
	 * BoardGameCollector constructor.
	 *
	 * @param string   $file     Plugin bootstrap file.
	 * @param Injector $injector Auryn\Injector instance.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-04-12
	 */
	public function __construct( string $file, Injector $injector ) {
		$this->plugin_file = $file;
		$this->injector    = $injector;
	}

	/**
	 * Kick off the plugin functionality!
	 *
	 * @throws ConfigException If misconfigured.
	 * @since 1.0.0
	 */
	public function run() {
		$this->file_path = plugin_dir_path( $this->plugin_file );

		// Check to see if it's time to run cron processes.
		CronService::maybe_schedule_cron();

		parent::run();
	}

	/**
	 * Register plugins services.
	 *
	 * This method overrides the one defined in OOPS-WP so we can use the Auryn DI container to create our objects.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-02-21
	 */
	protected function init_services() {
		$objects = array_map(
			function ( $object_classname ) {
				return [
					'namespace' => $object_classname,
					'object'    => $this->injector->make( $object_classname ),
				];
			},
			$this->services
		);

		return array_column( $objects, 'object', 'namespace' );
	}
}
