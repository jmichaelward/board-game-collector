<?php
/**
 * Main command for the plugin.
 *
 * @author Jeremy Ward <jeremy@jmichaelward.com>
 * @package JMichaelWard\BoardGameCollector\UI\Cli\Command
 * @since 2019-05-01
 */

namespace JMichaelWard\BoardGameCollector\UI\Cli\Command;

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
	 * @throws ExitException If process fails.
	 */
	public function update() {
		$this->progress_bar->run();

		try {
			$this->updater->update_collection();
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
			$games_query = new \WP_Query(
				[
					'post_type'      => 'bgc_game',
					'posts_per_page' => -1,
				]
			);

			foreach ( $this->updater->remove_collection( $games_query ) as $game_title ) {
				WP_CLI::success( "{$game_title} successfully deleted." );
			}
		} catch ( \Throwable $e ) {
			WP_CLI::error( $e->getMessage() );
		}
	}
}

