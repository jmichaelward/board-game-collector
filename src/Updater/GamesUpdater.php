<?php
namespace JMichaelWard\BoardGameCollector\Updater;

use JMichaelWard\BoardGameCollector\API\BoardGameGeek;
use JMichaelWard\BoardGameCollector\Model\Games\BGGGameAdapter;
use JMichaelWard\BoardGameCollector\Model\Games\GameData;
use JMichaelWard\BoardGameCollector\Admin\Settings;

/**
 * Class GamesUpdater
 *
 * @package JMichaelWard\BoardGameCollector
 */
class GamesUpdater {
	/**
	 * Plugin settings.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * BoardGameGeek API
	 *
	 * @var BoardGameGeek
	 */
	private $api;

	/**
	 * Username saved in plugin settings.
	 *
	 * @var string
	 */
	private $username;

	/**
	 * GamesUpdater constructor.
	 *
	 * @param BoardGameGeek $api      Instance of our BoardGameGeek API model.
	 * @param Settings      $settings Plugin settings.
	 */
	public function __construct( BoardGameGeek $api, Settings $settings ) {
		$this->api      = $api;
		$this->settings = $settings;
	}

	/**
	 * Hydrate the object with data.
	 */
	private function hydrate() {
		$data           = $this->settings->get_data();
		$this->username = sanitize_title( $data['bgg-username'] ?? '' );
	}

	/**
	 * Convert data into WordPress content.
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

		$this->hydrate();

		array_filter( $this->api->get_collection( $this->username ), [ $this, 'save_game_data' ] );
	}

	/**
	 * Save game data to WordPress.
	 *
	 * @param array $data Array data for a given game.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-04-13
	 */
	private function save_game_data( array $data ) {
		$game    = ( new BGGGameAdapter( $data ) )->get_game();
		$game_id = $this->game_exists( $game );

		$game_id ? $this->update_game( $game, array_pop( $game_id ) ) : $this->insert_game( $game );
	}

	/**
	 * Query WordPress to check whether the game is already in the database.
	 *
	 * @param GameData $game Interface for a game object.
	 *
	 * @return array
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

		return $posts;
	}

	/**
	 * Insert a new Games post into WordPress.
	 *
	 * @param GameData $game Interface for a game object.
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
			return;
		}

		$this->load_image( $id, $game->get_image_url(), $game->get_name() );
		wp_set_object_terms( $id, $game->get_statuses(), 'bgc_game_status' );

		// We'll save all the BGG meta data for reference.
		update_post_meta( $id, 'bgc_game_id', $game->get_bgg_id() );
		update_post_meta( $id, 'bgc_game_meta', $game );
	}

	/**
	 * Update the post meta and terms.
	 *
	 * @param GameData $game         Interface for a game object.
	 * @param int      $game_post_id ID of the bgc_game post.
	 */
	private function update_game( GameData $game, $game_post_id ) {
		update_post_meta( $game_post_id, 'bgc_game_meta', $game );
		wp_set_object_terms( $game_post_id, $game->get_statuses(), 'bgc_game_status' );
	}

	/**
	 * Sideloads the game's image into the WordPress media library.
	 *
	 * @param int    $id        Post ID to which to attach the image.
	 * @param string $image_url URL of the image asset.
	 * @param string $name      Name of the game.
	 *
	 * @return bool
	 */
	private function load_image( $id, $image_url, $name ) : bool {
		if ( ! function_exists( 'media_handle_sideload' ) ) {
			include_once ABSPATH . '/wp-admin/includes/image.php';
			include_once ABSPATH . '/wp-admin/includes/file.php';
			include_once ABSPATH . '/wp-admin/includes/media.php';
		}

		$tmp = download_url( $image_url );

		if ( is_wp_error( $tmp ) ) {
			return false;
		}

		$file_array = [];

		// Set variables for storage.
		// fix file filename for query strings.
		preg_match( '/[^\?]+\.(jpg|jpeg|gif|png)/i', $image_url, $matches );
		$file_array['name']     = basename( $matches[0] );
		$file_array['tmp_name'] = $tmp;

		// If error storing temporarily, unlink.
		if ( is_wp_error( $tmp ) ) {
			@unlink( $file_array['tmp_name'] ); // @codingStandardsIgnoreLine TODO: Remove PHP error suppression.
			$file_array['tmp_name'] = '';
		}

		$img = media_handle_sideload( $file_array, $id, '', [ 'post_name' => $name ] );

		// If error storing permanently, unlink.
		if ( is_wp_error( $img ) ) {
			@unlink( $file_array['tmp_name'] ); // @codingStandardsIgnoreLine TODO: Remove PHP error suppression.

			return false;
		}

		update_post_meta( $id, '_thumbnail_id', $img );

		return true;
	}
}
