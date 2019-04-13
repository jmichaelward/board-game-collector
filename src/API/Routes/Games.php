<?php
namespace JMichaelWard\BoardGameCollector\Api\Routes;

/**
 * Class Games
 *
 * Modifies the data returned by the default WordPress REST API endpoints.
 *
 * @package BGW\API\Endpoints
 */
class Games {
	/**
	 * Register data fields to return in the standard Games response.
	 */
	public function register_api_fields() {
		register_rest_field( 'bgc_game', 'metadata', [
			'get_callback' => function ( $post ) {
				return get_post_meta( $post['id'], 'bgc_game_meta' );
			},
		] );
	}
}
