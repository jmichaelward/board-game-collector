<?php
namespace JMichaelWard\BoardGameWeekly;

use JMichaelWard\BoardGameWeekly\Model\Games\BGGGame;
use JMichaelWard\BoardGameWeekly\Model\Games\GameDataInterface;

/**
 * Class GamesUpdater
 *
 * @package BGW\BoardGameWeekly
 */
class GamesUpdater {
	/**
	 * Username saved in plugin settings.
	 *
	 * @var string
	 */
	private $username;

	/**
	 * Plugin settings.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * BoardGameWeekly API endpoint.
	 *
	 * @var string
	 */
	private $endpoint = 'https://www.boardgamegeek.com/xmlapi2';

	/**
	 * GamesUpdater constructor.
	 *
	 * @param Settings $settings Plugin settings.
	 */
	public function __construct( Settings $settings ) {
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
	 * @param $data
	 *
	 * @return array|false|string
	 */
	public function convert_xml_to_json( $data ) {
		if ( ! $data ) {
			return wp_json_encode( array() );
		}

		libxml_use_internal_errors( true );

		$xml = simplexml_load_string( $data );

		if ( ! $xml )  {
			error_log( 'Could not retrieve BoardGameWeekly data at ' . time() );
		}

		$json  = wp_json_encode( $xml );
		$games = json_decode( $json, true );

		if ( ! isset( $games['item'] ) ) {
			return array();
		}

		return $games['item'];
	}

	/**
	 * Retrieve games data from the API.
	 */
	public function get_collection_data() {
		$games = get_transient( 'bgg_collection' );

		if ( ! $games ) {
			$data  = wp_remote_get( "{$this->endpoint}/collection?username={$this->username}" ); // @codingStandardsIgnoreLine
			$games = $this->convert_xml_to_json( wp_remote_retrieve_body( $data ) );

			if ( ! isset( $games ) ) {
				$games = [];
			}

			set_transient( 'bgg_collection', $games, Cron::INTERVAL_VALUE );
		}

		return $games;
	}

	/**
	 * Convert data into WordPress content.
	 */
	public function update_collection() {
		// Load required WordPress functionality.
		include_once ABSPATH . WPINC . '/pluggable.php';

		// @TODO Authorization.
		wp_set_auth_cookie( 1 );

		$this->hydrate();

		foreach ( $this->get_collection_data() as $data ) {
			$game = new BGGGame( $data );
			$game_post = $this->game_exists( $game );

			if ( ! $game_post ) {
				$this->insert_game( $game );

				continue;
			}

			if ( is_array( $game_post ) && count( $game_post ) === 1 && is_a( $game_post[0], '\WP_Post' ) ) {
				$this->update_game( $game, $game_post[0] );
			}
		}
	}

	/**
	 * Query WordPress to check whether the game is already in the database.
	 *
	 * @param GameDataInterface $game Interface for a game object.
	 *
	 * @return \WP_Post
	 */
	private function game_exists( GameDataInterface $game ) {
		$args = [
			'name'           => $game->get_id(), // @codingStandardsIgnoreLine
			'post_title'     => $game->get_name(),
			'post_type'      => 'bgw_game',
			'posts_per_page' => 1,
			'post_status'    => 'publish',
		];

		return get_posts( $args ); // @codingStandardsIgnoreLine - prefer get_posts in this scenario.
	}

	/**
	 * Insert a new Games post into WordPress.
	 *
	 * @param GameDataInterface $game Interface for a game object.
	 */
	private function insert_game( GameDataInterface $game ) {
		$args = [
			'post_type'   => 'bgw_game',
			'post_name'   => $game->get_id(), // @codingStandardsIgnoreLine
			'post_title'  => $game->get_name(),
			'post_status' => 'publish',
		];

		$id = wp_insert_post( $args );

		if ( ! $id ) {
			return;
		}

		$this->load_image( $id, $game->get_image_url(), $game->get_name() );
		wp_set_object_terms( $id, $game->get_statuses(), 'bgw_game_status' );

		// We'll save all the BGG meta data for reference.
		update_post_meta( $id, 'bgw_game_meta', $game->get_data() );
	}

	/**
	 * Update the post meta and terms.
	 *
	 * @param GameDataInterface $game Interface for a game object.
	 * @param \WP_Post          $game_post bgw_game post.
	 */
	private function update_game( GameDataInterface $game, \WP_Post $game_post ) {
		update_post_meta( $game_post->ID, 'bgw_game_meta', $game->get_data() );
		wp_set_object_terms( $game_post->ID, $game->get_statuses(), 'bgw_game_status' );
	}

	/**
	 * Sideloads the game's image into the WordPress media library.
	 *
	 * @param int    $id        Post ID to which to attach the image.
	 * @param string $image_url URL of the image asset.
	 * @param string $name      Name of the game.
	 *
	 * @return bool|int|object
	 */
	private function load_image( $id, $image_url, $name ) {
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
			@unlink( $file_array['tmp_name'] ); // TODO: Remove PHP error suppression.
			$file_array['tmp_name'] = '';
		}

		$img = media_handle_sideload(
			$file_array, $id,
			'',
			[
				'post_name' => $name,
			]
		);

		// If error storing permanently, unlink.
		if ( is_wp_error( $img ) ) {
			@unlink( $file_array['tmp_name'] ); // TODO: Remove PHP error suppression.

			return false;
		}

		update_post_meta( $id, '_thumbnail_id', $img );

		return true;
	}
}
