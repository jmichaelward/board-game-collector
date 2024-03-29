<?php
/**
 * Main command for the plugin.
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-05-01
 * @package JMichaelWard\BoardGameCollector\UI\Cli\Command
 */

namespace JMichaelWard\BoardGameCollector\UI\Cli\Command;

use JMichaelWard\BoardGameCollector\UI\Cli\ProgressBar;
use JMichaelWard\BoardGameCollector\Updater\GamesUpdater;
use WP_CLI;
use WP_CLI\ExitException;
use function JMichaelWard\BoardGameCollector\delete_user_transients;

/**
 * Command entrypoint for the Board Game Collector plugin.
 *
 * ## EXAMPLES
 *
 *     # Update Games in the WordPress database.
 *     $ wp bgc update
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
	 * Max number of attempts to try for updates.
	 */
	private const MAX_UPDATE_ATTEMPTS = 5;

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
			$attempts = 0;

			while (
			array_key_exists( 'processing', $this->updater->update_collection() )
			) {
				if ( self::MAX_UPDATE_ATTEMPTS === $attempts ) {
					WP_CLI::log( __( 'Looks like this is a pretty large collection. Try again later.',
					                 'bgcollector' ) );
				}

				$attempts ++;
				usleep( $this->updater::REQUEST_RETRY_MILLISECOND_WAIT );
			}

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

			delete_user_transients();
		} catch ( \Throwable $e ) {
			WP_CLI::error( $e->getMessage() );
		}
	}
}

