<?php
namespace JMichaelWard\BoardGameCollector;

use JMichaelWard\BoardGameCollector\Admin\Settings;
use JMichaelWard\BoardGameCollector\Content\Registrar as Content;
use JMichaelWard\BoardGameCollector\API\Registrar as API;
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
	 * Service for registering content post types and taxonomies.
	 *
	 * @var Content
	 */
	private $content;

	/**
	 * Service for setting up custom API routes.
	 *
	 * @var API
	 */
	private $api;

	/**
	 * Cron service.
	 *
	 * @var Cron
	 */
	public $cron;

	/**
	 * BoardGameCollector constructor.
	 */
	public function __construct() {
		$this->settings = new Settings();
		$this->content  = new Content();
		$this->api      = new API();
		$this->cron     = new Cron( new GamesUpdater( $this->settings ) );
	}

	/**
	 * Kick things off.
	 */
	public function run() {
		// Set up plugin events hooks.
		$this->settings->hooks();
		$this->api->hooks();
		$this->cron->hooks();

		// Check to see if it's time to run cron processes.
		$this->cron->maybe_schedule_cron();
	}

	/**
	 * Path to the application root.
	 *
	 * @return string
	 */
	public static function app_path() {
		return plugin_dir_path( dirname( __FILE__ ) );
	}
}
