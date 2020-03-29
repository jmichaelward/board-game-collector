<?php
/**
 * Model for the BoardGameGeek API.
 *
 * @TODO Consider whether this is the right location for this class.
 *
 * @package JMichaelWard\BoardGameCollector\Api
 */

namespace JMichaelWard\BoardGameCollector\Api;

use JMichaelWard\BoardGameCollector\Cron\CronService;

/**
 * Class BoardGameGeek
 *
 * @package JMichaelWard\BoardGameCollector\Api
 */
class BoardGameGeek {
	/**
	 * The name of the collection transient key.
	 */
	const COLLECTION_TRANSIENT_KEY = 'bgg_collection';

	/**
	 * Base path for the BoardGameGeek API.
	 *
	 * @var string
	 */
	private $base_path = 'https://www.boardgamegeek.com/xmlapi2';

	/**
	 * Attempt to retrieve a user's game collection from the API.
	 *
	 * @param string $username The user collection to retrieve.
	 *
	 * @return array|\WP_Error
	 */
	public function get_collection( string $username ) : array {
		$cached = get_transient( 'bgg_collection' );

		if ( $cached ) {
			return $cached;
		}

		$games = $this->get_games_from_api( $username );

		if ( ! $games ) {
			return [];
		}

		set_transient( self::COLLECTION_TRANSIENT_KEY, $games, CronService::INTERVAL_VALUE );

		return $games;
	}

	/**
	 * Get games from the BoardGameGeek API.
	 *
	 * @param string $username The username to query against.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-04-12
	 * @return array
	 */
	private function get_games_from_api( string $username ) : array {
		$request  = new Request( "{$this->base_path}/collection?username={$username}&stats=1" );
		$response = $request->make();

		if ( is_wp_error( $response ) ) {
			return [];
		}

		$games = $response->get_body();

		return $games['item'];
	}
}
