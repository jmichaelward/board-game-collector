<?php
/**
 *
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-09-02
 * @package JMichaelWard\BoardGameCollector\Api\Routes
 */

namespace JMichaelWard\BoardGameCollector\Api\Routes\Custom;

use JMichaelWard\BoardGameCollector\Admin\Settings\SettingsFields;
use JMichaelWard\BoardGameCollector\Api\ApiService;
use JMichaelWard\BoardGameCollector\Api\BoardGameGeek;
use JMichaelWard\BoardGameCollector\Api\Response;
use JMichaelWard\BoardGameCollector\Api\Routes\CustomRestRoute;
use JMichaelWard\BoardGameCollector\Updater\GamesUpdater;

/**
 * Class Collection
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
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
	 * BoardGameGeek API instance.
	 *
	 * @var BoardGameGeek
	 */
	private $bgg_api;

	/**
	 * GamesUpdater instance.
	 *
	 * @var GamesUpdater
	 */
	private $updater;

	/**
	 * Collection constructor.
	 *
	 * @param BoardGameGeek $bgg_api BoardGameGeek API instance.
	 * @param GamesUpdater  $updater GamesUpdater instance.
	 */
	public function __construct( BoardGameGeek $bgg_api, GamesUpdater $updater ) {
		$this->bgg_api = $bgg_api;
		$this->updater = $updater;
	}

	/**
	 * Register API routes with WordPress.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-09-01
	 */
	public function register_routes() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		register_rest_route(
			$this->namespace,
			$this->rest_base,
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'update_items' ],
				'args'                => [
					'username' => [
						'required'          => true,
						'validate_callback' => [ $this, 'validate_username' ],
					],
				],
				'permission_callback' => function ( \WP_REST_Request $request ) {
					return wp_verify_nonce( $request->get_header( 'x_wp_nonce' ), 'wp_rest' );
				},
			]
		);
	}

	/**
	 * Get the saved username from the settings page.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-10-04
	 * @return string
	 */
	private function get_saved_username() {
		return get_option( SettingsFields::SETTINGS_KEY )[ SettingsFields::USERNAME_KEY ] ?? '';
	}

	/**
	 * @param string $username
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-10-04
	 * @return bool
	 */
	public function validate_username( $username ) {
		return  ! empty( $username ) && $username === $this->get_saved_username();
	}

	/**
	 * Connect to the BoardGameGeek API and update the collection of Games within WordPress.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-09-01
	 */
	public function update_items() {
		$unprocessed = get_transient( BoardGameGeek::COLLECTION_TRANSIENT_KEY );

		if ( ! $unprocessed ) {
			$response = $this->bgg_api->request_user_collection( $this->get_saved_username() );

			if ( 202 === $response->get_status_code() ) {
				return new \WP_REST_Response( [ 'status' => 202 ], 202 );
			}

			set_transient( $this->bgg_api::COLLECTION_TRANSIENT_KEY, $response, 5 * MINUTE_IN_SECONDS );

			return $response;
		}

		return $this->process_remaining_games( $unprocessed );
	}

	/**
	 * @param Response $response API response to the games query.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-10-04
	 */
	private function process_remaining_games( Response $response ) {
		$games = $response->get_body()['item'] ?? [];
		$games_to_process = $this->get_games_to_process( $games );

		foreach ( $games_to_process as $index => $game ) {
			if ( $this->updater->save_game_data( $game ) ) {
				unset( $games[ $index ] );
			}
		}

		$games = array_values( $games );

		$new_response = new Response(['item' => $games]);

		set_transient( $this->bgg_api::COLLECTION_TRANSIENT_KEY, $new_response, 5 * MINUTE_IN_SECONDS );

		return $new_response;
	}

	/**
	 * @param array $games
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-10-04
	 * @return array
	 */
	private function get_games_to_process( array $games ) {
		return array_splice( $games, 0, ( count( $games ) <= 10 ?: 10 ) );
	}
}
