<?php
namespace JMichaelWard\BoardGameCollector\Api;

use JMichaelWard\BoardGameCollector\Cron\CronService;

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
	public function get_collection( string $username ) : array {
		return get_transient( 'bgg_collection' ) ?: $this->get_games_from_api( $username );
	}

	/**
	 * Get games from the BoardGameGeek API.
	 *
	 * @param string $username The username to query against.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-04-12
	 * @return array
	 */
	private function get_games_from_api( string $username ) : array {
		$response = wp_remote_get( "{$this->base_path}/collection?username={$username}&stats=1" );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return [];
		}

		$games = $this->convert_xml_to_json( wp_remote_retrieve_body( $response ) );

		set_transient( 'bgg_collection', $games, CronService::INTERVAL_VALUE );

		return $games['item'] ?? [];
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
