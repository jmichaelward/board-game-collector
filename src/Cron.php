<?php
namespace BGW\BoardGameGeek;

/**
 * Establishes a cron task for periodically hitting the BGG API.
 *
 * Class Cron
 *
 * @package BGW\BoardGameGeek
 */
class Cron {
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
	 * @param GamesUpdater $updater BGG Updater.
	 */
	public function __construct( GamesUpdater $updater ) {
		$this->updater = $updater;
	}

	/**
	 * Cron hooks.
	 */
	public function hooks() {
		// Setup the cron interval and the callback task.
		add_filter( 'cron_schedules', [ $this, 'add_interval' ] ); // @codingStandardsIgnoreLine
		add_action( 'bgw_collection_update', [ $this->updater, 'update_collection' ], 10, 1 );
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
				esc_html_x( '$1%s', 'bgw' ),
				Cron::INTERVAL_DESCRIPTION
			),
		];

		return $schedules;
	}

	/**
	 * Check to see if we need to update the games collection locally.
	 */
	public function maybe_schedule_cron() {
		if ( ! wp_next_scheduled( 'bgw_collection_update' ) ) {
			wp_schedule_event( time(), Cron::INTERVAL_NAME, 'bgw_collection_update' );
		}
	}
}
