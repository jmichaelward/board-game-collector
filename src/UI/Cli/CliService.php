<?php
namespace JMichaelWard\BoardGameCollector\UI\Cli;

use Auryn\Injector;
use JMichaelWard\BoardGameCollector\Model\Games\BggGame;
use JMichaelWard\BoardGameCollector\UI\Cli\Command\BgcCommand;
use JMichaelWard\BoardGameCollector\Updater\GamesUpdater;
use WebDevStudios\OopsWP\Structure\Service;
use WebDevStudios\OopsWP\Utility\Hookable;

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
	 * GamesUpdater instance.
	 *
	 * @var $updater
	 */
	private $updater;

	/**
	 * CliService constructor.
	 *
	 * @param Injector     $injector Auryn\Injector instance.
	 * @param GamesUpdater $updater  GamesUpdater instance.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-04-13
	 */
	public function __construct( Injector $injector, GamesUpdater $updater ) {
		$this->injector = $injector;
		$this->updater  = $updater;
	}

	/**
	 * Set of Cli commands registered to this plugin.s
	 *
	 * @var array
	 */
	private $commands = [
		'bgc' => BgcCommand::class,
	];

	/**
	 * Run the CliService.
	 *
	 * It's necessary to share the GamesUpdater here so that it can be accessed by the CLI commands.
	 *
	 * @throws \Auryn\ConfigException If Auyrn is misconfigured.
	 */
	public function run() {
		parent::run();
		$this->injector->share( $this->updater );
	}

	/**
	 * Initialize this service with WordPress.
	 */
	public function register_hooks() {
		add_action( 'cli_init', [ $this, 'register_commands' ] );
		add_action( 'bgc_notify_image_processed', [ $this, 'notify_image_processed' ], 10, 3 );
	}

	/**
	 * Register this plugin's set of custom commands.
	 *
	 * @throws \Exception If the command could not be added.
	 */
	public function register_commands() {
		foreach ( $this->commands as $command_name => $command_class ) {
			try {
				$command = $this->injector->make( $command_class );

				if ( in_array( Hookable::class, class_implements( $command_class ), true ) ) {
					$command->register_hooks();
				}

				\WP_CLI::add_command( $command_name, $command );
			} catch ( \Exception $e ) {
				\WP_CLI::error( $e->getMessage() );
			}
		}
	}

	/**
	 * @param int     $image_id
	 * @param int     $game_id
	 * @param BggGame $game
	 */
	public function notify_image_processed( int $image_id, int $game_id, BggGame $game ) {
		$game_name = $game->get_name();

		$image_id
			? \WP_CLI::success( "Set featured image ID {$image_id} on game ID {$game_id}: {$game_name}." )
			: \WP_CLI::error( "Failed to process image for {$game_name}." );
	}
}
