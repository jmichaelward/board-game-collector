<?php
namespace JMichaelWard\BoardGameCollector\Service;

use JMichaelWard\BoardGameCollector\Updater\GamesUpdater;
use WebDevStudios\OopsWP\Structure\Service;

/**
 * Establishes a cron task for periodically hitting the BGG API.
 *
 * Class Cron
 *
 * @package JMichaelWard\BoardGameCollector
 */
class Cron extends Service {
	/**
	 * Name of the interval.
	 */
	const INTERVAL_NAME = 'five_minutes';

	/**
	 * Value of the interval.
	 */
	const INTERVAL_VALUE = 300;

	/**
	 * Description of the interval.
	 */
	const INTERVAL_DESCRIPTION = 'Every five minutes';

	/**
	 * BGG Updater class.
	 *
	 * @var GamesUpdater
	 */
	private $updater;

	/**
	 * Cron constructor.
	 *
	 * @param GamesUpdater $updater Instance of the GamesUpdater.
	 */
	public function __construct( GamesUpdater $updater ) {
		$this->updater = $updater;
	}

	/**
	 * Cron hooks.
	 */
	public function register_hooks() {
		// Setup the cron interval and the callback task.
		add_filter( 'cron_schedules', [ $this, 'add_interval' ] ); // @codingStandardsIgnoreLine
		add_action( 'bgc_collection_update', [ $this->updater, 'update_collection' ], 10, 1 );
	}

	/**
	 * Add a periodic interval to the WP_Cron schedule.
	 *
	 * @param array $schedules WP_Cron schedules.
	 *
	 * @return array
	 */
	public function add_interval( $schedules ) {
		$schedules[ Cron::INTERVAL_NAME ] = [
			'interval' => Cron::INTERVAL_VALUE,
			'display'  => sprintf( // Translators: $1%s is the interval description.
				esc_html_x( '$1%s', 'bgc' ),
				Cron::INTERVAL_DESCRIPTION
			),
		];

		return $schedules;
	}

	/**
	 * Check to see if we need to update the games collection locally.
	 */
	public static function maybe_schedule_cron() {
		if ( ! wp_next_scheduled( 'bgc_collection_update' ) ) {
			wp_schedule_event( time(), Cron::INTERVAL_NAME, 'bgc_collection_update' );
		}
	}
}
