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
	 * @param GamesUpdater $updater The GamesUpdater instance.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-04-13
	 */
	public function __construct( GamesUpdater $updater ) {
		$this->updater = $updater;
	}

	/**
	 * Update the Games post type with data from BoardGameGeek.
	 */
	public function update() {
		$this->progress_bar = new ProgressBar();
		$this->progress_bar->run();

		$this->updater->update_collection();
	}
}

