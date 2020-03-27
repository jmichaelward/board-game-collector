<?php
namespace JMichaelWard\BoardGameCollector\UI\Cli;

use Auryn\Injector;
use JMichaelWard\BoardGameCollector\UI\Cli\Command\BgcCommand;
use WebDevStudios\OopsWP\Structure\Service;

/**
 * Class CliService
 *
 * @package JMichaelWard\BoardGameCollector\Service
 */
class CliService extends Service {
	/**
	 * Auryn\Injector instance.
	 *
	 * @var Injector
	 * @since 2019-04-13
	 */
	private $injector;

	/**
	 * CliService constructor.
	 *
	 * @param Injector $injector Auryn\Injector instance.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-04-13
	 */
	public function __construct( Injector $injector ) {
		$this->injector = $injector;
	}

	/**
	 * Set of Cli commands registered to this plugin.
	 *
	 * @var array
	 */
	private $commands = [
		'bgc' => BgcCommand::class,
	];

	/**
	 * Initialize this service with WordPress.
	 */
	public function register_hooks() {
		add_action( 'cli_init', [ $this, 'register_commands' ] );
	}

	/**
	 * Register this plugin's set of custom commands.
	 *
	 * @throws \Exception If the command could not be added.
	 */
	public function register_commands() {
		foreach ( $this->commands as $command_name => $command_class ) {
			try {
				\WP_CLI::add_command( $command_name, $this->injector->make( $command_class ) );
			} catch ( \Exception $e ) {
				\WP_CLI::error( $e->getMessage() );
			}
		}
	}
}
