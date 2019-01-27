<?php
namespace JMichaelWard\BoardGameCollector\Command;

use JMichaelWard\BoardGameCollector\API\BoardGameGeek;
use JMichaelWard\BoardGameCollector\Service\Settings;
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
	 */
	public function __construct() {
		$this->updater = new GamesUpdater( new BoardGameGeek(), new Settings() );
	}
	/**
	 * Update the Games post type with data from BoardGameGeek.
	 */
	public function update() {
		$this->updater->update_collection();
	}
}

