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
use JMichaelWard\BoardGameCollector\Api\Response;
use JMichaelWard\BoardGameCollector\Api\Routes\CustomRestRoute;
use JMichaelWard\BoardGameCollector\Updater\GamesUpdater;
use WP_Query;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Class Collection
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-09-02
 * @package JMichaelWard\BoardGameCollector\Api\Routes
 */
class Collection extends CustomRestRoute {
	/**
	 * Transient key for remaining games to process.
	 */
	const REMAINING_GAMES_TRANSIENT_KEY = 'bgc_remaining_games_to_process';

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
	 * @return void
	 */
	public function register_routes(): void {
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
				'permission_callback' => [ $this, 'permission_callback_verify_nonce' ],
			]
		);

		register_rest_route(
			$this->namespace,
			"{$this->rest_base}/images",
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'update_images' ],
				'permission_callback' => [ $this, 'permission_callback_verify_nonce' ],
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
	public function validate_username( $username ): bool {
		return ! empty( $username ) && strtolower( $username ) === strtolower( $this->settings->get_username() );
	}

	/**
	 * Connect to the BoardGameGeek API and update the collection of Games within WordPress.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-09-01
	 */
	public function update_items(): WP_REST_Response {
		$unprocessed = get_transient( self::REMAINING_GAMES_TRANSIENT_KEY );

		if ( false === $unprocessed || $unprocessed instanceof Response ) {
			$response = $this->bgg_api->request_user_collection( $this->settings->get_username() );

			if ( 202 === $response->get_status_code() ) {
				return new WP_REST_Response( [ 'games' => [], 'status' => 202 ], 202 );
			}

			$unprocessed = $response->get_body()['item'] ?? [];

			set_transient( self::REMAINING_GAMES_TRANSIENT_KEY, $unprocessed, 1 * DAY_IN_SECONDS );

			return new WP_REST_Response( [ 'games' => $unprocessed, 'status' => 200 ], 200 );
		}

		return new WP_REST_Response( [ 'games' => $this->process_remaining_games( $unprocessed ), 'status' => 200 ],
		                             200 );
	}

	/**
	 * Route callback to download images and set them as featured on a game.
	 *
	 * @param WP_REST_Request $request Class instance.
	 *
	 * @return array
	 */
	public function update_images( WP_REST_Request $request ): array {
		// Get all games that don't have featured images.
		$query = new WP_Query(
			[
				'post_type'  => 'bgc_game',
				'meta_query' => [
					[
						'key'     => '_thumbnail_id',
						'compare' => 'NOT EXISTS',
					],
				],
			]
		);

		$games = $query->get_posts();

		if ( empty( $games ) ) {
			return [];
		}

		$games_data = array_map( function ( $game ) {
			return get_post_meta( $game->ID, 'bgc_game_meta', true );
		}, $games );

		$this->updater->process_collection_images( $games_data );

		return $games;
	}

	/**
	 * Gets a subset of games from a collection and creates post objects in WordPress.
	 *
	 * The purpose of this method is to allow batch processing of games via the API, essentially
	 * looping through multiple internal API calls until the number of games remaining to add is
	 * zero.
	 *
	 * Logic here is a little confusing, so I'm leaving a note for now should I need to revisit.
	 *
	 * 1. Get a batch of games for which we need to create records.
	 * 2. Pass those games to the GamesUpdater to create their records.
	 * 3. Iterate through the batch, and remove all the processed records from the larger set.
	 * 4. Set a new transient with the remaining set of games to process on the next iteration.
	 *
	 * @param array $games API response to the games query.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-10-04
	 * @return array Games from BoardGameGeek which are not yet in WordPress.
	 */
	private function process_remaining_games( array $games ): array {
		if ( ! $games ) {
			return [];
		}

		$games_to_process = $this->get_games_to_process( $games );
		$unprocessed      = $this->updater->update_collection( $games_to_process );

		foreach ( $games_to_process as $index => $game ) {
			if ( ! array_key_exists( $index, $unprocessed ) ) {
				unset( $games[ $index ] );
			}
		}

		$games = array_values( $games );

		set_transient( self::REMAINING_GAMES_TRANSIENT_KEY, $games, 1 * DAY_IN_SECONDS );

		return $games;
	}

	/**
	 * @param array $games
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-10-04
	 * @return array
	 */
	private function get_games_to_process( array $games ): array {
		return array_splice( $games, 0, ( count( $games ) <= 10 ?: 10 ) );
	}
}
