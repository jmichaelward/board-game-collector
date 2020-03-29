<?php
/**
 * Wrapper object for managing requests to BoardGameGeek.
 *
 * @package JMichaelWard\BoardGameCollector\Api
 */

namespace JMichaelWard\BoardGameCollector\Api;

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
		// Try to make the request.
		// If we get back an array, return a Response object.
		// If we get back a WP_Error, wrap it in a Response object.
		try {
			$request_method = $this->methods[ $this->method ];
			$data           = $request_method( $this->url );

			$this->response = new Response( $data );
		} catch ( \Throwable $e ) {
			// Invalid request.
			$this->response = new Response( [] );
		}

		return $this->response;
	}
}
