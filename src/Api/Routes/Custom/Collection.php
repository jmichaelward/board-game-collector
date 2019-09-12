<?php
/**
 *
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @since   2019-09-02
 * @package JMichaelWard\BoardGameCollector\Api\Routes
 */

namespace JMichaelWard\BoardGameCollector\Api\Routes\Custom;

use JMichaelWard\BoardGameCollector\Api\BoardGameGeek;
use JMichaelWard\BoardGameCollector\Api\Routes\CustomRestRoute;

/**
 * Class Collection
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @since   2019-09-02
 * @package JMichaelWard\BoardGameCollector\Api\Routes
 */
class Collection extends CustomRestRoute {
	/**
	 * REST base for this route.
	 *
	 * @var string
	 * @since 2019-09-01
	 */
	protected $rest_base = 'collection';

	/**
	 * Register API routes with WordPress.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-09-01
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			"/{$this->rest_base}",
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'update_items' ],
				'permission_callback' => [ $this, 'verify_nonce' ],
			]
		);
	}

	/**
	 * @TODO Add nonce verification.
	 *
	 * @param \WP_REST_Request $request The WordPress REST request.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-09-01
	 * @return bool
	 */
	public function verify_nonce( \WP_REST_Request $request ) {
		$nonce = $request->get_param( 'wp_nonce' );

		return true;
	}

	/**
	 * Connect to the BoardGameGeek API and update the collection of Games within WordPress.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-09-01
	 * @return void
	 */
	public function update_items() {
		$api = new BoardGameGeek();

		delete_transient( BoardGameGeek::COLLECTION_TRANSIENT_KEY );

		$request = $api->get_collection( get_option( 'bgc-settings' )[ 'bgg-username' ] );

		return $request;
	}
}
