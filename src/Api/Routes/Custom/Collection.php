<?php
/**
 *
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-09-02
 * @package JMichaelWard\BoardGameCollector\Api\Routes
 */

namespace JMichaelWard\BoardGameCollector\Api\Routes\Custom;

use JMichaelWard\BoardGameCollector\Admin\Settings;
use JMichaelWard\BoardGameCollector\Admin\Settings\SettingsFields;
use JMichaelWard\BoardGameCollector\Api\BoardGameGeek;
use JMichaelWard\BoardGameCollector\Api\Routes\CustomRestRoute;
use JMichaelWard\BoardGameCollector\Model\Games\BggGame;
use JMichaelWard\BoardGameCollector\Model\Games\BggGameAdapter;
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
	 * Register API routes with WordPress.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-09-01
	 */
	public function register_routes() {
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
					return wp_verify_nonce( $request->get_param( 'nonce' ), 'wp_rest' );
				},
			]
		);
	}

	/**
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-10-04
	 * @return string
	 */
	private function get_saved_username() {
		$options = get_option( SettingsFields::SETTINGS_KEY );

		return get_option( SettingsFields::SETTINGS_KEY )[ SettingsFields::USERNAME_KEY ] ?? '';
	}

	/**
	 * @param $username
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
			$api        = new BoardGameGeek();
			$collection = $api->get_user_collection( $this->get_saved_username() );

			if ( isset( $collection['status'] ) && 202 === $collection['status'] ) {
				return new \WP_REST_Response( [ 'status' => 202 ], 202 );
			}

			set_transient( BoardGameGeek::COLLECTION_TRANSIENT_KEY, $collection, 5 * MINUTE_IN_SECONDS );

			return $collection;
		}

		return $this->process_remaining_games( $unprocessed );
	}

	/**
	 * @param $games
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-10-04
	 */
	private function process_remaining_games( $games ) {
		$updater          = new GamesUpdater();
		$games_to_process = $this->get_games_to_process( $games );

		foreach ( $games_to_process as $index => $game ) {
			if ( $updater->save_game_data( $game ) ) {
				unset( $games[ $index ] );
			}
		}

		$games = array_values( $games );

		set_transient( BoardGameGeek::COLLECTION_TRANSIENT_KEY, $games, 5 * MINUTE_IN_SECONDS );

		return $games;
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
