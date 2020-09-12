<?php
/**
 * Collection of helper methods.
 *
 * @package JMichaelWard\BoardGameCollector
 */

namespace JMichaelWard\BoardGameCollector;

use JMichaelWard\BoardGameCollector\Api\BoardGameGeek;
use JMichaelWard\BoardGameCollector\Api\Routes\Custom\Collection;
use JMichaelWard\BoardGameCollector\Updater\GamesUpdater;

/**
 * Delete transients pertaining to a given BoardGameGeek username.
 *
 * @author Jeremy Ward <jeremy@jmichaelward.com>
 * @since  2020-09-12
 * @return void
 */
function delete_user_transients() {
	delete_transient( BoardGameGeek::COLLECTION_TRANSIENT_KEY );
	delete_transient( Collection::REMAINING_GAMES_TRANSIENT_KEY );
	delete_option( GamesUpdater::GAMES_INDEX_OPTION_KEY );
}
