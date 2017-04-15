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
	public $settings;

	/**
	 * BGG Cron class.
	 *
	 * @var Cron
	 */
	public $cron;

	/**
	 * BoardGameGeek constructor.
	 */
	public function __construct() {
		require_once plugin_dir_path( __FILE__ ) . 'GamesUpdater.php';
		require_once plugin_dir_path( __FILE__ ) . 'Cron.php';
		require_once plugin_dir_path( __FILE__ ) . 'Settings.php';

		$this->settings = new Settings();
		$this->cron     = new Cron( new GamesUpdater( $this->settings ) );
	}

	/**
	 * Kick things off.
	 */
	public function run() {
		$this->settings->hooks();
		$this->cron->hooks();
	}
}
