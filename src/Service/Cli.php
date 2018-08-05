<?php


namespace JMichaelWard\BoardGameCollector\Service;


use JMichaelWard\BoardGameCollector\Command\BggCommand;

class Cli extends Service {
	/**
	 * @var
	 */
	private $commands = [];

	/**
	 * Cli constructor.
	 */
	public function __construct() {
		if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
			return;
		}

		$this->attach_commands();
	}

	/**
	 * Attach commands that will be initialized to this class.
	 */
	private function attach_commands() {
		$this->commands = [
			BggCommand::class,
		];
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
		foreach ( $this->commands as $command_class ) {
			$command = new $command_class();

			\WP_CLI::add_command( $command::get_name(), $command_class );
		}
	}
}
