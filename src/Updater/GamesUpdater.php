<?php
/**
 * This object is responsible for processing the Games data and creating posts in WordPress.
 *
 * It depends on the BoardGameGeek API to retrieve data, and the WordPress settings to know which
 * data to act upon.
 *
 * @package JMichaelWard\BoardGameCollector\Updater
 */

namespace JMichaelWard\BoardGameCollector\Updater;

use JMichaelWard\BoardGameCollector\Api\BoardGameGeek;
use JMichaelWard\BoardGameCollector\Model\Games\BggGameAdapter;
use JMichaelWard\BoardGameCollector\Model\Games\GameData;
use JMichaelWard\BoardGameCollector\Admin\Settings;
use \Exception;
use \InvalidArgumentException;

/**
 * Class GamesUpdater
 *
 * @package JMichaelWard\BoardGameCollector
 */
class GamesUpdater {
	/**
	 * Key for the games index.
	 *
	 * This index maps a BoardGameGeek game ID to the ID in WordPress to facilitate lookup.
	 */
	const GAMES_INDEX_OPTION_KEY = 'bgc_collection_index';

	/**
	 * BoardGameGeek API.
	 *
	 * @var BoardGameGeek
	 */
	private $api;

	/**
	 * Plugin settings.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * Game data adapter.
	 *
	 * @var BggGameAdapter
	 */
	private $adapter;

	/**
	 * Instance of the ImageProcessor class.
	 *
	 * @var ImageProcessor
	 */
	private $image_processor;

	/**
	 * A associative array mapping BoardGameGeek IDs to WordPress post IDs.
	 *
	 * [
	 *      $bgg_id => [
	 *          'post_id' => $wordpress_id (bgc_game),
	 *          'image_id' => $wordpress_id (attachment)
	 *     ]
	 * ]
	 *
	 * @var array
	 */
	private $games_index;

	/**
	 * GamesUpdater constructor.
	 *
	 * @param BoardGameGeek  $api             Instance of our BoardGameGeek API model.
	 * @param Settings       $settings        Plugin settings.
	 * @param BggGameAdapter $adapter         Adapter for BoardGameGeek data.
	 * @param ImageProcessor $image_processor Instance of ImageProcessor class.
	 */
	public function __construct(
		BoardGameGeek $api,
		Settings $settings,
		BggGameAdapter $adapter,
		ImageProcessor $image_processor
	) {
		$this->api             = $api;
		$this->settings        = $settings;
		$this->adapter         = $adapter;
		$this->image_processor = $image_processor;
	}

	/**
	 * Save game data to WordPress.
	 *
	 * @param array $data Array data for a given game.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-04-13
	 * @return int
	 */
	public function save_game_data( array $data ) {
		$game    = $this->adapter->get_game( $data );
		$game_id = $this->game_exists( $game );

		if ( ! $game_id ) {
			$this->remove_game_from_index( $game->get_bgg_id() );
		}

		return $game_id ? $this->update_game( $game_id, $game ) : $this->insert_game( $game );
	}

	/**
	 * Convert data into WordPress content.
	 *
	 * @throws Exception|InvalidArgumentException If API request requirements are unmet.
	 */
	public function update_collection() {
		// Load required WordPress functionality.
		include_once ABSPATH . WPINC . '/pluggable.php';

		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			// @TODO Authorization.
			wp_set_auth_cookie( 1 );
		}

		if ( ! WP_CLI && ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		$games = $this->api->get_user_collection( $this->settings->get_username() );

		$this->games_index = $this->get_games_index();

		$this->process_games_data( $games );
	}

	/**
	 * Remove posts from a set of query results.
	 *
	 * @return \Generator
	 */
	public function remove_collection() {
		foreach ( $this->get_wp_query_all_games()->get_posts() as $post ) {
			$attachment_id = get_post_thumbnail_id( $post->ID );

			if ( $attachment_id ) {
				wp_delete_attachment( $attachment_id, true );
			}

			$result = wp_delete_post( $post->ID );

			yield [
				'status' => $result instanceof \WP_Post ? 'success' : 'fail',
				'post'   => $result instanceof \WP_Post ? $result : $post,
			];
		}
	}

	/**
	 * Query all games.
	 *
	 * @return \WP_Query
	 */
	private function get_wp_query_all_games() : \WP_Query {
		return new \WP_Query(
			[
				'post_type'      => 'bgc_game',
				'posts_per_page' => -1,
			]
		);
	}

	/**
	 * Process the retrieved games data.
	 *
	 * @param array $games Games retrieved from BoardGameGeek.
	 */
	private function process_games_data( array $games ) {
		do_action( 'bgc_setup_progress_bar', count( $games ) );

		foreach ( $games as $game ) {
			$this->save_game_data( $game );

			do_action( 'bgc_tick_progress_bar' );
		}

		$this->save_index_updates();

		do_action( 'bgc_finish_progress_bar' );

		$this->process_images( $games );
	}

	/**
	 * Process image data for the games.
	 *
	 * @TODO WIP. This method probably belongs in the ImageProcessor itself.
	 *
	 * @param array $games Array of games data.
	 */
	private function process_images( array $games ) {
		$this->games_index = $this->get_games_index();

		$data = array_filter(
			array_map(
				function( $game_data ) {
					$game = $this->adapter->get_game( $game_data );
					$id   = $this->games_index[ $game->get_bgg_id() ]['post_id'] ?? 0;

					if ( ! $id ) {
						return [];
					}

					return [
						'game' => $game,
						'id'   => $id,
					];
				},
				$games
			)
		);

		foreach ( $data as $game ) {
			$id = $this->image_processor->process_game_image( $game['id'], $game['game'] );

			do_action( 'bgc_notify_image_processed', $id, $game['id'], $game['game'] );
		}
	}

	/**
	 * Query WordPress to check whether the game is already in the database.
	 *
	 * @param GameData $game Interface for a game object.
	 *
	 * @return int
	 */
	private function game_exists( GameData $game ) {
		$id   = $this->get_wordpress_id_from_index( $game );
		$post = get_post( $id );

		if ( $post instanceof \WP_Post ) {
			return $post->ID;
		}

		$args = array_merge(
			[
				'post_type'      => 'bgc_game',
				'fields'         => 'ids',
				'posts_per_page' => 1,
				'post_status'    => 'publish',
			],
			$game->get_unique_identifiers()
		);

		$posts = ( new \WP_Query( $args ) )->get_posts();

		wp_reset_postdata();

		return array_pop( $posts );
	}

	/**
	 * Get the WordPress post ID for a game within the games index.
	 *
	 * @param GameData $game Instance of GameData.
	 *
	 * @return int
	 */
	private function get_wordpress_id_from_index( GameData $game ) {
		$bgg_id = $game->get_bgg_id();

		if ( ! array_key_exists( $bgg_id, $this->games_index ) ) {
			return 0;
		}

		return $this->games_index[ $bgg_id ]['post_id'] ?? 0;
	}

	/**
	 * Insert a new Games post into WordPress.
	 *
	 * @param GameData $game Interface for a game object.
	 *
	 * @return int
	 */
	private function insert_game( GameData $game ) {
		$game_id = wp_insert_post(
			[
				'post_type'   => 'bgc_game',
				'post_name'   => sanitize_title( $game->get_name() ),
				'post_title'  => $game->get_name(),
				'post_status' => 'publish',
			]
		);

		if ( ! $game_id ) {
			return 0;
		}

		// We'll save all the BGG meta data for reference.
		$this->save_game_meta( $game_id, $game );
		$this->add_game_to_index( $game->get_bgg_id(), $game_id );

		return $game_id;
	}

	/**
	 * Update the post meta and terms.
	 *
	 * @param int      $game_id WordPress ID of the game post.
	 * @param GameData $game    Interface for a game object.
	 *
	 * @return int
	 */
	private function update_game( $game_id, GameData $game ) {
		$this->save_game_meta( $game_id, $game );

		return $game_id;
	}

	/**
	 * Save the metadata of a game to its post.
	 *
	 * @param int      $game_id The WordPress ID of the game.
	 * @param GameData $game    The adapted game data from BoardGameGeek.
	 */
	private function save_game_meta( $game_id, GameData $game ) {
		update_post_meta( $game_id, 'bgc_game_id', $game->get_bgg_id() );
		update_post_meta( $game_id, 'bgc_game_meta', $game );
		wp_set_object_terms( $game_id, $game->get_statuses(), 'bgc_game_status' );
	}


	/**
	 * Get the saved games index.
	 *
	 * @return array
	 */
	private function get_games_index() : array {
		return get_option( self::GAMES_INDEX_OPTION_KEY, [] );
	}

	/**
	 * Update the games index with the newly-saved game's data.
	 *
	 * These updates are saved to memory only. save_index_updates gets called at the end of the process.
	 *
	 * @see GamesUpdater::save_index_updates()
	 *
	 * @param int $bgg_id The ID of the game on BoardGameGeek.
	 * @param int $wordpress_id The ID of the game in WordPress.
	 */
	private function add_game_to_index( $bgg_id, $wordpress_id ) {
		$this->games_index[ $bgg_id ]['post_id'] = $wordpress_id;
	}

	/**
	 * Remove a game from the games index.
	 *
	 * @param int $bgg_id The ID of the game on BoardGameGeek.
	 */
	private function remove_game_from_index( $bgg_id ) {
		unset( $this->games_index[ $bgg_id ] );
	}

	/**
	 * Save updates to the games index.
	 */
	private function save_index_updates() {
		update_option( self::GAMES_INDEX_OPTION_KEY, $this->games_index );
	}
}
