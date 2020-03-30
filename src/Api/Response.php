<?php
/**
 * Wrapper class for BoardGameGeek responses.
 *
 * @package JMichaelWard\BoardGameCollector\Api\Bgg
 */

namespace JMichaelWard\BoardGameCollector\Api;

/**
 * Class Response
 *
 * @package JMichaelWard\BoardGameCollector\Api
 */
class Response {
	/**
	 * The response data.
	 *
	 * @var array
	 */
	private $data;

	/**
	 * Response constructor.
	 *
	 * @param array $data The response data.
	 */
	public function __construct( array $data = [] ) {
		$this->data = $data;
	}

	/**
	 * Get the headers from the response.
	 *
	 * @return array|\Requests_Utility_CaseInsensitiveDictionary
	 */
	public function get_headers() {
		return wp_remote_retrieve_headers( $this->data );
	}

	/**
	 * Get the response data.
	 *
	 * @return array
	 */
	public function get_body() {
		return $this->convert_xml_to_json( wp_remote_retrieve_body( $this->data ) );
	}

	/**
	 * Get the status code from the response.
	 *
	 * @return int
	 */
	public function get_status_code() {
		return wp_remote_retrieve_response_code( $this->data );
	}

	/**
	 * Get request processing error.
	 *
	 * @return string
	 */
	public function get_error() : string {
		return $this->data['error'] ?? '';
	}

	/**
	 * Convert the BoardGameGeek API XML response to JSON.
	 *
	 * @param string $data XML data.
	 *
	 * @return array
	 */
	private function convert_xml_to_json( string $data ) : array {
		if ( ! $data ) {
			return [];
		}

		libxml_use_internal_errors( true );

		$xml = simplexml_load_string( $data );

		// @codingStandardsIgnoreLine
		return $xml ? json_decode( json_encode( $xml ), true ) : json_decode( $data, true );
	}
}
