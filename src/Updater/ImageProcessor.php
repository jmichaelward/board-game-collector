<?php
/**
 * Handles download and management of game images from BoardGameGeek.
 *
 * @author Jeremy Ward <jeremy@jmichaelward.com>
 * @package JMichaelWard\BoardGameCollector\Updater
 */

namespace JMichaelWard\BoardGameCollector\Updater;

use JMichaelWard\BoardGameCollector\Model\Games\BggGame;
use WebDevStudios\OopsWP\Structure\Service;
use function \get_current_screen;

/**
 * Class ImageProcessor
 *
 * @package JMichaelWard\BoardGameCollector\Updater
 */
class ImageProcessor extends Service {
	/**
	 * WordPress post ID for the game.
	 *
	 * @var int
	 */
	private $game_id;

	/**
	 * Processed BoardGameGeek data.
	 *
	 * @var BggGame
	 */
	private $game_data;

	/**
	 * Register hooks for image processing.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2020-09-13
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'wp_head', [ $this, 'maybe_sideload_image' ] );
	}

	/**
	 * Sideload the game's image on single pages if one doesn't exist.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2020-09-13
	 * @return void
	 */
	public function maybe_sideload_image() {
		global $post;

		if (
			is_admin() && 'bgc_game' !== get_current_screen()->id ||
			is_single( $post ) && 'bgc_game' !== $post->post_type
		) {
			return;
		}

		$this->game_data = get_post_meta( $post->ID, 'bgc_game_meta', true );

		if ( ! is_a( $this->game_data, BggGame::class ) ) {
			return;
		}

		$this->game_id = $post->ID;

		$image_id = $this->get_image_id_from_game_id();

		if ( $image_id ) {
			$this->set_image_meta( $image_id );
			return;
		}

		$this->load_image( $post->ID, $this->game_data->get_image_url(), $this->game_data->get_name() );
	}

	/**
	 * Process the game's image and return its ID.
	 *
	 * This method checks whether a game already has a featured image and simply
	 * returns its ID if it does. Otherwise, it attempts to download the image
	 * from BoardGameGeek, sideload it into WordPress, attach it to the game,
	 * and set some metadata on the image itself for reference. Finally, we
	 * return the ID.
	 *
	 * @param int     $game_id   WordPress post ID of the game.
	 * @param BggGame $game_data The game's data from BoardGameGeek.
	 *
	 * @return int ID of the processed image.
	 */
	public function process_game_image( int $game_id, BggGame $game_data ) {
		$this->game_id   = $game_id;
		$this->game_data = $game_data;
		$image_id        = $this->get_featured_image_id();

		if ( ! $image_id ) {
			$image_id = $this->load_image( $this->game_id, $this->game_data->get_image_url(), $this->game_data->get_name() );
		}

		return $image_id;
	}

	/**
	 * Check for the existence of the image based on the BoardGameGeek game ID.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-05-01
	 * @return int
	 */
	private function get_image_id_from_game_id() {
		$query = new \WP_Query(
			[
				'fields'      => 'ids',
				'post_type'   => 'attachment',
				'post_status' => 'any',
				'meta_query'  => [ // @codingStandardsIgnoreLine
					[
						'key'   => '_bgc_game_id',
						'value' => $this->game_id,
					],
				],
			]
		);

		$result = $query->get_posts();

		wp_reset_postdata();

		if ( ! empty( $result ) ) {
			return $result[0];
		}

		return 0;
	}

	/**
	 * Sideloads the game's image into the WordPress media library.
	 *
	 * @param int    $id        Post ID to which to attach the image.
	 * @param string $image_url URL of the image asset.
	 * @param string $name      Name of the game.
	 *
	 * @return int The post ID for the inserted attachment.
	 */
	private function load_image( $id, $image_url, $name ) {
		if ( ! function_exists( 'media_handle_sideload' ) ) {
			include_once ABSPATH . '/wp-admin/includes/image.php';
			include_once ABSPATH . '/wp-admin/includes/file.php';
			include_once ABSPATH . '/wp-admin/includes/media.php';
		}

		$tmp = download_url( $image_url );

		if ( is_wp_error( $tmp ) ) {
			return 0;
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

		$img_id = media_handle_sideload( $file_array, $id, '', [ 'post_name' => $name ] );

		// If error storing permanently, unlink.
		if ( is_wp_error( $img_id ) ) {
			@unlink( $file_array['tmp_name'] ); // @codingStandardsIgnoreLine TODO: Remove PHP error suppression.

			return 0;
		}

		$this->set_image_meta( $img_id );

		return $img_id;
	}

	/**
	 * Get a game's image ID.
	 *
	 * @return int The post ID of the attachment.
	 */
	private function get_featured_image_id() {
		return get_post_thumbnail_id( $this->game_id );
	}

	/**
	 * Set post meta on the game image.
	 *
	 * @param int $image_id WordPress post ID for a game image.
	 */
	private function set_image_meta( int $image_id ) : void {
		if ( ! $image_id ) {
			return;
		}

		update_post_meta( $this->game_id, '_thumbnail_id', $image_id );
		update_post_meta( $image_id, '_bgc_orig_image_url', $this->game_data->get_image_url() );
		update_post_meta( $image_id, '_bgc_game_id', $this->game_id );
	}
}
