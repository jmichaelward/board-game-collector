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
	 * GamesUpdater constructor.
	 *
	 * @param BoardGameGeek  $api      Instance of our BoardGameGeek API model.
	 * @param Settings       $settings Plugin settings.
	 * @param BggGameAdapter $adapter  Adapter for BoardGameGeek data.
	 */
	public function __construct( BoardGameGeek $api, Settings $settings, BggGameAdapter $adapter ) {
		$this->api      = $api;
		$this->settings = $settings;
		$this->adapter  = $adapter;
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

		$game_id ? $this->update_game( $game, $game_id ) : $this->insert_game( $game );

		do_action( 'bgc_tick_progress_bar' );

		return $game_id;
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

		do_action( 'bgc_setup_progress_bar', count( $games ) );

		array_filter( $games, [ $this, 'save_game_data' ] );

		do_action( 'bgc_finish_progress_bar' );
	}

	/**
	 * Query WordPress to check whether the game is already in the database.
	 *
	 * @param GameData $game Interface for a game object.
	 *
	 * @return int
	 */
	private function game_exists( GameData $game ) {
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
	 * Insert a new Games post into WordPress.
	 *
	 * @param GameData $game Interface for a game object.
	 *
	 * @return int
	 */
	private function insert_game( GameData $game ) {
		$id = wp_insert_post(
			[
				'post_type'   => 'bgc_game',
				'post_name'   => sanitize_title( $game->get_name() ),
				'post_title'  => $game->get_name(),
				'post_status' => 'publish',
			]
		);

		if ( ! $id ) {
			return 0;
		}

		$image_handler = new ImageProcessor();

		$image_handler->set_featured_image( $id, $game );

		wp_set_object_terms( $id, $game->get_statuses(), 'bgc_game_status' );

		// We'll save all the BGG meta data for reference.
		update_post_meta( $id, 'bgc_game_id', $game->get_bgg_id() );
		update_post_meta( $id, 'bgc_game_meta', $game );

		return $id;
	}

	/**
	 * Update the post meta and terms.
	 *
	 * @param GameData $game         Interface for a game object.
	 * @param int      $game_post_id ID of the bgc_game post.
	 *
	 * @return int
	 */
	private function update_game( GameData $game, $game_post_id ) {
		update_post_meta( $game_post_id, 'bgc_game_meta', $game );
		wp_set_object_terms( $game_post_id, $game->get_statuses(), 'bgc_game_status' );

		return $game_post_id;
	}
}
