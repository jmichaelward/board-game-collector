<?php
namespace BGW\BoardGameGeek;

/**
 * Class BoardGameGeek
 *
 * @package BGW\BoardGameGeek
 */
class BoardGameGeek {
	/**
	 * Settings data.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * BoardGameGeek constructor.
	 */
	public function __construct() {
		require_once plugin_dir_path( __FILE__ ) . '/GamesUpdater.php';
		require_once plugin_dir_path( __FILE__ ) . 'Settings.php';

		$this->settings = new Settings();
	}

	/**
	 * Kick things off.
	 */
	public function run() {
		$this->hooks();
		$this->schedule_cron();
	}

	/**
	 * Plugin hooks.
	 */
	private function hooks() {
		// Run hooks for plugin settings.
		$this->settings->hooks();

		// Cron scheduler.
		add_action( 'bgw_collection_update', [ new GamesUpdater( $this->settings ), 'update_collection' ], 99, 1 );
	}

	/**
	 * Schedules a call to the BoardGameGeek API once per hour in order to update the local database of games.
	 */
	private function schedule_cron() {
		if ( ! wp_next_scheduled( 'bgw_collection_update' ) ) {
			wp_schedule_event( time(), 'hourly', 'bgw_collection_update' );
		}
	}
}
