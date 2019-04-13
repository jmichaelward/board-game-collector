<?php
namespace JMichaelWard\BoardGameCollector\Service;

use JMichaelWard\BoardGameCollector\Command\BgcCommand;
use WebDevStudios\OopsWP\Structure\Service;

/**
 * Class Cli
 *
 * @package JMichaelWard\BoardGameCollector\Service
 */
class Cli extends Service {
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
				\WP_CLI::add_command( $command_name, $command_class );
			} catch ( \Exception $e ) {
				\WP_CLI::error( $e->getMessage() );
			}
		}
	}
}
