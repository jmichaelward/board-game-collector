<?php
namespace JMichaelWard\BoardGameCollector;

use JMichaelWard\BoardGameCollector\Admin\Settings;
use JMichaelWard\BoardGameCollector\Content\Registrar;
use JMichaelWard\BoardGameCollector\Updater\Cron;
use JMichaelWard\BoardGameCollector\Updater\GamesUpdater;

/**
 * Class BoardGameCollector
 *
 * @package JMichaelWard\BoardGameCollector
 */
class BoardGameCollector {
	/**
	 * Settings data.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * Wrapper class for registering content post types and taxonomies.
	 *
	 * @var Content
	 */
	private $content;

	/**
	 * BGG Cron class.
	 *
	 * @var Cron
	 */
	public $cron;

	/**
	 * BoardGameCollector constructor.
	 */
	public function __construct() {
		$this->settings = new Settings();
		$this->content  = new Registrar();
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
