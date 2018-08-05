<?php
namespace JMichaelWard\BoardGameCollector\Service;

use JMichaelWard\BoardGameCollector\Command\BgcCommand;

/**
 * Class Cli
 *
 * @package JMichaelWard\BoardGameCollector\Service
 */
class Cli extends Service {
	/**
	 * Cli constructor.
	 */
	public function __construct() {
		if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
			return;
		}
	}

	/**
	 * Initialize this service with WordPress.
	 */
	public function register_hooks() {
		add_action( 'init', [ $this, 'register_commands' ] );
	}

	/**
	 * Register this plugin's set of custom commands.
	 *
	 * @throws \Exception
	 */
	public function register_commands() {
		\WP_CLI::add_command( 'bgc', BgcCommand::class );
	}
}
