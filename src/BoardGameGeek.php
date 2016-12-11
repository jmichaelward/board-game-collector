<?php
namespace BGW\BoardGameGeek;

require_once dirname( __FILE__ ) . '/GamesUpdater.php';

/**
 * Class BoardGameGeek
 * @package BGW\BoardGameGeek
 */
class BoardGameGeek {
    /**
     * Kick things off.
     */
    public function run() {
        $this->hooks();
        $this->schedule_cron();
    }

    /**
     *
     */
    private function hooks() {
        add_action( 'bgw_collection_update', [ new GamesUpdater, 'update_collection' ] );
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
