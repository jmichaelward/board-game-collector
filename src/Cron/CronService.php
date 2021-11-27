<?php
/**
 * Cron service to update games periodically.
 *
 * @TODO    Set this as an option under Settings.
 *
 * @package JMichaelWard\BoardGameCollector\Cron
 */

namespace JMichaelWard\BoardGameCollector\Cron;

use JMichaelWard\BoardGameCollector\Updater\GamesUpdater;
use WebDevStudios\OopsWP\Structure\Service;

/**
 * Establishes a cron task for periodically hitting the BGG API.
 *
 * Class CronService
 *
 * @package JMichaelWard\BoardGameCollector
 */
class CronService extends Service {
	/**
	 * Name of the interval.
	 */
	private const INTERVAL_NAME = 'five_minutes';

	/**
	 * Value of the interval.
	 */
	public const INTERVAL_VALUE = 300;

	/**
	 * Description of the interval.
	 */
	private const INTERVAL_DESCRIPTION = 'Every five minutes';

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
		add_action( 'bgc_collection_update', [ $this, 'update_collection' ], 10, 1 );
	}

	/**
	 * Add a periodic interval to the WP_Cron schedule.
	 *
	 * @param array $schedules WP_Cron schedules.
	 *
	 * @return array
	 */
	public function add_interval( $schedules ) {
		$schedules[ self::INTERVAL_NAME ] = [
			'interval' => self::INTERVAL_VALUE,
			'display'  => sprintf( // Translators: $1%s is the interval description.
				esc_html_x( '$1%s', 'bgc' ),
				self::INTERVAL_DESCRIPTION
			),
		];

		return $schedules;
	}

	/**
	 * Check to see if we need to update the games collection locally.
	 */
	public static function maybe_schedule_cron() {
		if ( ! wp_next_scheduled( 'bgc_collection_update' ) ) {
			wp_schedule_event( time(), self::INTERVAL_NAME, 'bgc_collection_update' );
		}
	}

	/**
	 * Process game updates.
	 *
	 * @throws \Exception
	 */
	public function update_collection() {
		// Load required WordPress functionality.
		include_once ABSPATH . WPINC . '/pluggable.php';

		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			// @TODO Authorization.
			wp_set_auth_cookie( 1 );
		}

		if ( ! WP_CLI && ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		$this->updater->update_collection();
	}
}
