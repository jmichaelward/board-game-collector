<?php
namespace JMichaelWard\BoardGameCollector\Cli\Command;

use JMichaelWard\BoardGameCollector\Admin\Settings;
use JMichaelWard\BoardGameCollector\API\BoardGameGeek;
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
class BgcCommand extends \WP_CLI_Command {
	/**
	 * Instance of GamesUpdater class.
	 *
	 * @var GamesUpdater
	 */
	private $updater;

	/**
	 * BgcCommand constructor.
	 *
	 * @param GamesUpdater $updater The GamesUpdater instance.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-04-13
	 */
	public function __construct( GamesUpdater $updater ) {
		parent::__construct();

		$this->updater = $updater;
	}

	/**
	 * Update the Games post type with data from BoardGameGeek.
	 */
	public function update() {
		$this->updater->update_collection();
	}
}

