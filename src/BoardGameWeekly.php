<?php
namespace JMichaelWard\BoardGameWeekly;

/**
 * Class BoardGameWeekly
 *
 * @package BGW\BoardGameWeekly
 */
class BoardGameWeekly {
	/**
	 * Settings data.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * @var
	 */
	private $content;

	/**
	 * BGG Cron class.
	 *
	 * @var Cron
	 */
	public $cron;

	/**
	 * BoardGameWeekly constructor.
	 */
	public function __construct() {
		$this->settings = new Settings();
		$this->content  = new Content();
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
