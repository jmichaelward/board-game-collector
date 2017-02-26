<?php
namespace BGW\BoardGameGeek;

/**
 * Class GamesUpdater
 *
 * @package BGW\BoardGameGeek
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
	 * BoardGameGeek API endpoint.
	 *
	 * @var string
	 */
	private $endpoint;

	/**
	 * GamesUpdater constructor.
	 *
	 * @param Settings $settings Plugin settings.
	 */
	public function __construct( Settings $settings ) {
		require_once plugin_dir_path( __FILE__ ) . 'Settings.php';

		$this->settings = $settings;
	}

	/**
	 * Hydrate the object with data.
	 */
	private function hydrate() {
		$data           = $this->settings->get_data();
		$this->username = esc_attr( isset( $data['bgg-username'] ) ? $data['bgg-username'] : '' );
		$this->endpoint = esc_url( 'https://bgg-json.azurewebsites.net/collection/' . $this->username );
	}

	/**
	 * Retrieve games data from the API.
	 */
	public function get_data() {
		$data = get_transient( 'bgg_collection' );

		if ( ! $data ) {
			$data  = wp_remote_get( $this->endpoint ); // @codingStandardsIgnoreLine
			$games = json_decode( wp_remote_retrieve_body( $data ) );

			if ( ! $games ) {
				$games = [];
			}

			set_transient( 'bgg_collection', $games, 5 * MINUTE_IN_SECONDS );
		}

		return $data;
	}

	/**
	 * Convert data into WordPress content.
	 */
	public function update_collection() {
		// Load required WordPress functionality.
		include_once ABSPATH . WPINC . '/pluggable.php';

		wp_set_auth_cookie( 1 );

		if ( ! function_exists( 'media_handle_sideload' ) ) {
			include_once ABSPATH . '/wp-admin/includes/image.php';
			include_once ABSPATH . '/wp-admin/includes/file.php';
			include_once ABSPATH . '/wp-admin/includes/media.php';
		}

		$this->hydrate();

		foreach ( $this->get_data() as $game ) {
			if ( $this->meets_requirements( $game ) ) {
				$this->insert_game( $game );
			}
		}
	}

	/**
	 * Determine whether a Game meets the requirements to be added to WordPress.
	 *
	 * @param \stdClass $game A single game object.
	 *
	 * @return bool
	 */
	private function meets_requirements( $game ) {
		return $game->owned && ! $game->isExpansion && ! $this->game_exists( $game ); // @codingStandardsIgnoreLine
	}

	/**
	 * Query WordPress to check whether the game is already in the database.
	 *
	 * @param \stdClass $game A game from the API response.
	 *
	 * @return bool
	 */
	private function game_exists( $game ) {
		$args = [
			'name'           => $game->gameId, // @codingStandardsIgnoreLine
			'post_title'     => $game->name,
			'post_type'      => 'bgw_game',
			'posts_per_page' => 1,
			'post_status'    => 'publish',
		];

		return get_posts( $args ); // @codingStandardsIgnoreLine - prefer get_posts in this scenario.
	}

	/**
	 * Insert a new Games post into WordPress.
	 *
	 * @param \stdClass $game A game object from the API.
	 */
	private function insert_game( $game ) {
		$args = [
			'post_type'   => 'bgw_game',
			'post_name'   => $game->gameId, // @codingStandardsIgnoreLine
			'post_title'  => $game->name,
			'post_status' => 'publish',
		];

		$id = wp_insert_post( $args );

		if ( $id ) {
			$this->load_image( $id, $game->image );
			wp_set_object_terms( $id, [ 'owned' ], 'bgw_game_status' );
		}

		// We'll save all the BGG meta data for reference.
		update_post_meta( $id, 'bgw_game_meta', $game );
	}

	/**
	 * Define the ownership level of the game.
	 *
	 * @param \stdClass $game A game object returned by the API.
	 *
	 * @return array
	 * TODO: We want to define ownership on a game so that it can be categorized on post insertion.
	 */
	private function define_ownership( $game ) {
		if ( $game->owned ) {
			return [
				'bgw_game_status' => 'owned',
			];
		}

		return [
			'bgw_game_status' => 'wishlist',
		];
	}

	/**
	 * Sideloads the game's image into the WordPress media library.
	 *
	 * @param int    $id        Post ID to which to attach the image.
	 * @param string $image_url URL of the image asset.
	 *
	 * @return bool|int|object
	 */
	private function load_image( $id, $image_url ) {
		$tmp = download_url( 'http:' . $image_url );

		if ( is_wp_error( $tmp ) ) {
			return false;
		}

		$file_array = [];

		// Set variables for storage.
		// fix file filename for query strings.
		preg_match( '/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $image_url, $matches );
		$file_array['name']     = basename( $matches[0] );
		$file_array['tmp_name'] = $tmp;

		// If error storing temporarily, unlink.
		if ( is_wp_error( $tmp ) ) {
			@unlink( $file_array['tmp_name'] ); // TODO: Remove PHP error suppression.
			$file_array['tmp_name'] = '';
		}

		$img = media_handle_sideload( $file_array, $id, '' );

		// If error storing permanently, unlink.
		if ( is_wp_error( $img ) ) {
			@unlink( $file_array['tmp_name'] ); // TODO: Remove PHP error suppression.

			return false;
		}

		update_post_meta( $id, '_thumbnail_id', $img );

		return true;
	}
}