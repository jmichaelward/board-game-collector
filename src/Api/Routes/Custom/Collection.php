<?php
/**
 *
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-09-02
 * @package JMichaelWard\BoardGameCollector\Api\Routes
 */

namespace JMichaelWard\BoardGameCollector\Api\Routes\Custom;

use JMichaelWard\BoardGameCollector\Admin\Settings\SettingsPage;
use JMichaelWard\BoardGameCollector\Api\BoardGameGeek;
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
	 * SettingsPage instance.
	 *
	 * @var SettingsPage
	 */
	private $settings;

	/**
	 * GamesUpdater instance.
	 *
	 * @var GamesUpdater
	 */
	private $updater;

	/**
	 * Collection constructor.
	 *
	 * @param BoardGameGeek $bgg_api  BoardGameGeek API instance.
	 * @param SettingsPage  $settings SettingsPage instance.
	 * @param GamesUpdater  $updater  GamesUpdater instance.
	 */
	public function __construct( BoardGameGeek $bgg_api, SettingsPage $settings, GamesUpdater $updater ) {
		$this->bgg_api  = $bgg_api;
		$this->settings = $settings;
		$this->updater  = $updater;
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
				'permission_callback' => [ $this, 'permission_callback_verify_nonce' ]
			]
		);

		register_rest_route(
			$this->namespace,
			"{$this->rest_base}/images",
			[
				'methods' => \WP_REST_Server::CREATABLE,
				'callback' => [ $this, 'update_images' ],
				'permission_callback' => [ $this, 'permission_callback_verify_nonce' ]
			]
		);
	}

	/**
	 * @param string $username
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-10-04
	 * @return bool
	 */
	public function validate_username( $username ) {
		return ! empty( $username ) && strtolower( $username ) === strtolower( $this->settings->get_username() );
	}

	/**
	 * Connect to the BoardGameGeek API and update the collection of Games within WordPress.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-09-01
	 */
	public function update_items() {
		$unprocessed = get_transient( BoardGameGeek::COLLECTION_TRANSIENT_KEY );

		if ( false === $unprocessed ) {
			$response = $this->bgg_api->request_user_collection( $this->settings->get_username() );

			if ( 202 === $response->get_status_code() ) {
				return new \WP_REST_Response( [ 'games' => [], 'status' => 202 ], 202 );
			}

			$unprocessed = $response->get_body()['item'] ?? [];

			set_transient( $this->bgg_api::COLLECTION_TRANSIENT_KEY, $unprocessed, 5 * MINUTE_IN_SECONDS );

			return new \WP_REST_Response( [ 'games' => $unprocessed, 'status' => 200 ], 200 );
		}

		return new \WP_REST_Response( [ 'games' => $this->process_remaining_games( $unprocessed ), 'status' => 200 ], 200 );
	}

	/**
	 * Route callback to download images and set them as featured on a game.
	 *
	 * @param \WP_REST_Request $request
	 */
	public function update_images( \WP_REST_Request $request ) {
		return [];
	}

	/**
	 * @param array $games API response to the games query.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-10-04
	 */
	private function process_remaining_games( array $games ) {
		if ( ! $games ) {
			return [];
		}

		$games_to_process = $this->get_games_to_process( $games );

		foreach ( $games_to_process as $index => $game ) {
			if ( $this->updater->save_game_data( $game ) ) {
				unset( $games[ $index ] );
			}
		}

		$games = array_values( $games );

		set_transient( $this->bgg_api::COLLECTION_TRANSIENT_KEY, $games, 5 * MINUTE_IN_SECONDS );

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
