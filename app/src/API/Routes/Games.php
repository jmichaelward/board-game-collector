<?php
namespace JMichaelWard\BoardGameCollector\API\Routes;

/**
 * Class Games
 *
 * @package BGW\API\Endpoints
 */
class Games extends \WP_REST_Controller {
	/**
	 * Register custom API routes for games.
	 *
	 * TODO: Probably instead roll this in with the Games post type.
	 */
	public function register_routes() {
		register_rest_route( 'bgc/v1', '/games/', [
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => [ $this, 'get_items' ],
		] );
	}


	/**
	 * Get all games.
	 *
	 * @param \WP_REST_Request $request The REST request.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_items( $request ) {
		delete_transient( 'bgc_games_list' );
		$titles = get_transient( 'bgc_games_list' );

		if ( ! $titles ) {
			$new_request = new \WP_REST_Request(
				\WP_REST_Server::READABLE,
				'/wp/v2/games'
			);

			$response = rest_do_request( $new_request );

			if ( 404 === $response->get_status() ) {
				return new \WP_Error(
					'no_games_found',
					'No Games!',
					[
						'status' => 404,
					]
				);
			}

			$titles = [];
			$data   = $response->get_data();

			foreach ( $data as $game ) {
				$meta  = get_post_meta( $game['id'], 'bgc_game_meta', true );
				unset( $meta['@attributes'] );
				$meta['image'] = get_the_post_thumbnail_url( $game['id'], 'url' );
				$meta['thumbnail'] = get_the_post_thumbnail_url( $game['id'], 'medium' );

				$titles[] = $meta;
			}

			set_transient( 'bgc_games_list', $titles, 1 * MINUTE_IN_SECONDS );
		}

		return new \WP_REST_Response( $titles );
	}
}
