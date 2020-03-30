<?php
/**
 * Wrapper object for managing requests to BoardGameGeek.
 *
 * @package JMichaelWard\BoardGameCollector\Api
 */

namespace JMichaelWard\BoardGameCollector\Api;

use WP_Error;
use Throwable;

/**
 * Class Request
 *
 * @package JMichaelWard\BoardGameCollector\Api
 */
class Request {
	/**
	 * URL for the request.
	 *
	 * @var string
	 */
	private $url;

	/**
	 * HTTP request method.
	 *
	 * @var string
	 */
	private $method;

	/**
	 * Collection of supported methods.
	 *
	 * @var array
	 */
	private $methods = [
		'GET' => '\wp_remote_get',
	];

	/**
	 * WordPress HTTP response data.
	 *
	 * @var array
	 */
	private $response;

	/**
	 * Request constructor.
	 *
	 * @param string $url    URL for the request.
	 * @param string $method HTTP request method.
	 */
	public function __construct( string $url, string $method = 'GET' ) {
		$this->url    = $url;
		$this->method = strtoupper( $method );
	}

	/**
	 * Make the request.
	 *
	 * @return Response
	 */
	public function make() {
		try {
			$request_method = $this->methods[ $this->method ];
			$data           = $request_method( $this->url );

			$this->response = $this->has_errors( $data ) ? $this->get_error_response( $data ) : new Response( $data );
		} catch ( \Throwable $e ) {
			// Invalid request.
			$this->response = $this->get_error_response( $e );
		}

		return $this->response;
	}

	/**
	 * Check whether the data has errors.
	 *
	 * @param \WP_Error $data Error data.
	 *
	 * @return bool
	 */
	private function has_errors( $data ) {
		return is_wp_error( $data ) || ! is_array( $data );
	}

	/**
	 * Get a response object which can signal errors to the UI.
	 *
	 * @param Throwable|WP_Error $data Some kind of error.
	 *
	 * @return Response
	 */
	private function get_error_response( $data ) {
		switch ( $data ) {
			case is_wp_error( $data ):
				/* @var WP_Error $data A WP_Error instance. */
				return new Response( [ 'error' => $data->get_error_message() ] );
			case $data instanceof \Throwable:
				/* @var Throwable $data Throwable error or exception. */
				return new Response( [ 'error' => $data->getMessage() ] );
			default:
				return new Response( [ 'error' => 'An unknown error occurred.' ] );
		}
	}
}
