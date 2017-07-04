<?php
namespace JMichaelWard\BoardGameCollector\API;

/**
 * Class BoardGameGeek
 *
 * @package JMichaelWard\BoardGameCollector\API
 */
class BoardGameGeek {
	/**
	 * @var string
	 */
	private $base_path = 'https://www.boardgamegeek.com/xmlapi2';

	/**
	 * @param string $username
	 *
	 * @return array|\WP_Error
	 */
	public function get_collection( $username ) {
		return wp_remote_get( "{$this->base_path}/collection?username={$username}&stats=1" ); // @codingStandardsIgnoreLine
	}

	/**
	 * @param $data
	 *
	 * @return array
	 */
	public function convert_xml_to_json( $data ) {
		if ( ! $data ) {
			return [];
		}

		libxml_use_internal_errors( true );

		$xml = simplexml_load_string( $data );

		if ( ! $xml )  {
			error_log( 'Could not retrieve BoardGameGeek data at ' . time() );
		}

		$json  = wp_json_encode( $xml );
		$games = json_decode( $json, true );

		if ( ! isset( $games['item'] ) ) {
			return [];
		}

		return $games['item'];
	}
}
