<?php
namespace JMichaelWard\BoardGameCollector\API;

use JMichaelWard\BoardGameCollector\Updater\Cron;

/**
 * Class BoardGameGeek
 *
 * @package JMichaelWard\BoardGameCollector\API
 */
class BoardGameGeek {
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
	public function get_collection( $username ) {
		if ( $games = get_transient( 'bgg_collection' ) ) {
			return $games;
		}

		$games = wp_remote_get( "{$this->base_path}/collection?username={$username}&stats=1" );

		if ( is_wp_error( $games ) || '200' !== wp_remote_retrieve_response_code( $games ) ) {
			return [];
		}

		$games = $this->convert_xml_to_json( wp_remote_retrieve_body( $games ) );
		$games = $games['item'] ?? [];

		set_transient( 'bgg_collection', $games, Cron::INTERVAL_VALUE );

		return $games;
	}

	/**
	 * Convert the BoardGameGeek API XML response to JSON.
	 *
	 * @param string $data XML data.
	 *
	 * @return array
	 */
	public function convert_xml_to_json( $data ) {
		if ( ! $data ) {
			return [];
		}

		libxml_use_internal_errors( true );

		$xml = simplexml_load_string( $data );

		if ( ! $xml ) {
			error_log( 'Could not retrieve BoardGameGeek data at ' . time() );
		}

		$json  = wp_json_encode( $xml );

		return json_decode( $json, true );
	}
}
