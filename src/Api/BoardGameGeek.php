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

		return $cached ? $cached : $this->get_games_from_api( $username );
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
		$response = wp_remote_get( "{$this->base_path}/collection?username={$username}&stats=1" );
		$status   = wp_remote_retrieve_response_code( $response );

		if ( is_wp_error( $response ) || ! in_array( $status, [ 200, 202 ], true ) ) {
			return [];
		}

		$games = $this->convert_xml_to_json( wp_remote_retrieve_body( $response ) );

		if ( ! isset( $games['item'] ) ) {
			return [];
		}

		set_transient( 'bgg_collection', $games['item'] ?? [], CronService::INTERVAL_VALUE );

		return $games['item'];
	}

	/**
	 * Convert the BoardGameGeek API XML response to JSON.
	 *
	 * @param string $data XML data.
	 *
	 * @return array
	 */
	private function convert_xml_to_json( $data ) : array {
		if ( ! $data ) {
			return [];
		}

		libxml_use_internal_errors( true );

		$xml = simplexml_load_string( $data );

		if ( ! $xml ) {
			return [];
		}

		return json_decode( wp_json_encode( $xml ), true );
	}
}
