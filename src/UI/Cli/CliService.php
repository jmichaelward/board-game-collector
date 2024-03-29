<?php
namespace JMichaelWard\BoardGameCollector\UI\Cli;

use JMichaelWard\BoardGameCollector\Model\Games\BggGame;
use JMichaelWard\BoardGameCollector\UI\Cli\Command\BgcCommand;
use JMichaelWard\BoardGameCollector\Updater\GamesUpdater;
use JMichaelWard\BoardGameCollector\Utility\FactoryService;
use JMichaelWard\BoardGameCollector\Utility\Instantiator;
use WebDevStudios\OopsWP\Utility\Hookable;

/**
 * Class CliService
 *
 * @package JMichaelWard\BoardGameCollector\Service
 */
class CliService extends FactoryService {
	use Instantiator;

	/**
	 * GamesUpdater instance.
	 *
	 * @var $updater
	 */
	private $updater;

	/**
	 * CliService constructor.
	 *
	 * @param GamesUpdater $updater  GamesUpdater instance.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-04-13
	 */
	public function __construct( GamesUpdater $updater ) {
		$this->updater = $updater;
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
	 */
	public function run() {
		parent::run();
		// $this->injector->share( $this->updater );
	}

	/**
	 * Initialize this service with WordPress.
	 */
	public function register_hooks() {
		add_action( 'cli_init', [ $this, 'register_commands' ] );

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			add_action( 'bgc_notify_image_processed', [ $this, 'notify_image_processed' ], 10, 3 );
			add_action( 'bgc_notify_collection_processing', [ $this, 'notify_collection_processing' ] );
		}
	}

	/**
	 * Register this plugin's set of custom commands.
	 *
	 * @throws \Exception If the command could not be added.
	 */
	public function register_commands() {
		foreach ( $this->commands as $command_name => $command_class ) {
			try {
				$command = $this->create( $command_class );

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
	 * Generate a notification that a game's image has been processed.
	 *
	 * @param int     $image_id WordPress ID of the image.
	 * @param int     $game_id  WordPress ID of the game.
	 * @param BggGame $game     The BggGame object.
	 */
	public function notify_image_processed( int $image_id, int $game_id, BggGame $game ) {
		$game_name = $game->get_name();

		$image_id
			? \WP_CLI::success( "Set featured image ID {$image_id} on game ID {$game_id}: {$game_name}." )
			: \WP_CLI::error( "Failed to process image for {$game_name}." );
	}

	/**
	 * Generate a notification that the collection request is being processed.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2020-09-12
	 * @return void
	 */
	public function notify_collection_processing() {
		\WP_CLI::success(
			__(
			// @codingStandardsIgnoreLine - integer value.
				"Request received. Large collections take longer to process -- trying again in {$this->updater->get_request_retry_length_in_seconds()} seconds.",
				'bgcollector'
			)
		);
	}
}
