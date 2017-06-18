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
	 * BGG Cron class.
	 *
	 * @var Cron
	 */
	public $cron;

	/**
	 * BoardGameGeek constructor.
	 */
	public function __construct() {
		$this->settings = new Settings();
		$this->cron     = new Cron( new GamesUpdater( $this->settings ) );
	}

	/**
	 * Kick things off.
	 */
	public function run() {
		$this->settings->hooks();
		$this->cron->hooks();
		$this->cron->maybe_schedule_cron();
	}
}
