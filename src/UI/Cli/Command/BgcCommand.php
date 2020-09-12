<?php
/**
 * Main command for the plugin.
 *
 * @author Jeremy Ward <jeremy@jmichaelward.com>
 * @package JMichaelWard\BoardGameCollector\UI\Cli\Command
 * @since 2019-05-01
 */

namespace JMichaelWard\BoardGameCollector\UI\Cli\Command;

use JMichaelWard\BoardGameCollector\Api\BoardGameGeek;
use JMichaelWard\BoardGameCollector\Api\Routes\Custom\Collection;
use JMichaelWard\BoardGameCollector\UI\Cli\ProgressBar;
use JMichaelWard\BoardGameCollector\Updater\GamesUpdater;
use WP_CLI;
use WP_CLI\ExitException;

/**
 * Command entrypoint for the Board Game Collector plugin.
 *
 * ## EXAMPLES
 *
 *     # Update Games in the WordPress database.
 *     $ wp bgg update
 *     Success: Games posts created in WordPress.
 *
 * @when after_wp_config_load
 */
class BgcCommand {
	/**
	 * Instance of GamesUpdater class.
	 *
	 * @var GamesUpdater
	 */
	private $updater;

	/**
	 * The progress bar.
	 *
	 * @var ProgressBar
	 * @since 2019-05-01
	 */
	private $progress_bar;

	/**
	 * BgcCommand constructor.
	 *
	 * @param GamesUpdater $updater      The GamesUpdater instance.
	 * @param ProgressBar  $progress_bar ProgressBar instance.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-04-13
	 */
	public function __construct( GamesUpdater $updater, ProgressBar $progress_bar ) {
		$this->updater      = $updater;
		$this->progress_bar = $progress_bar;
	}

	/**
	 * Update the Games post type with data from BoardGameGeek.
	 *
	 * ## OPTIONS
	 * <username>
	 *
	 * [--with-images]
	 * : Download and set the game's box art as the featured image when updating. This can take a long time depending
	 * on the size of the collection.
	 *
	 * @param array $args    Command arguments.
	 * @param array $options Command options.
	 *
	 * @throws ExitException If process fails.
	 */
	public function update( array $args, array $options ) {
		$this->progress_bar->run();

		try {
			$this->updater->update_collection();

			if ( isset( $options['with-images'] ) ) {
				$this->updater->process_collection_images();
			}
		} catch ( \Throwable $e ) {
			WP_CLI::error( $e->getMessage() );
		}
	}

	/**
	 * Clear games and associated images from WordPress.
	 */
	public function reset() {
		// Load required WordPress functionality.
		include_once ABSPATH . WPINC . '/pluggable.php';

		try {
			foreach ( $this->updater->remove_collection() as $result ) {
				'success' === $result['status']
					? WP_CLI::success( "{$result['post']->post_title} successfully deleted." )
					: WP_CLI::warning( "Failed to delete {$result['post']->post_title}." );
			}

			delete_transient( BoardGameGeek::COLLECTION_TRANSIENT_KEY );
			delete_transient( Collection::REMAINING_GAMES_TRANSIENT_KEY );
			delete_option( GamesUpdater::GAMES_INDEX_OPTION_KEY );
		} catch ( \Throwable $e ) {
			WP_CLI::error( $e->getMessage() );
		}
	}
}

