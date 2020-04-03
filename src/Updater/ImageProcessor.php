<?php
/**
 * Handles download and management of game images from BoardGameGeek.
 *
 * @author Jeremy Ward <jeremy@jmichaelward.com>
 * @package JMichaelWard\BoardGameCollector\Updater
 */

namespace JMichaelWard\BoardGameCollector\Updater;

use JMichaelWard\BoardGameCollector\Model\Games\GameData;

/**
 * Class ImageProcessor
 *
 * @package JMichaelWard\BoardGameCollector\Updater
 */
class ImageProcessor {
	/**
	 * Check for the existence of the image based on its URL.
	 *
	 * @param string $image_url The remote URL of the image.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-05-01
	 * @return int
	 */
	private function get_image_id_from_url( string $image_url ) {
		$query = new \WP_Query(
			[
				'fields'      => 'ids',
				'post_type'   => 'attachment',
				'post_status' => 'inherit',
				'meta_query'  => [ // @codingStandardsIgnoreLine
					[
						'key'   => '_bgc_orig_image_url',
						'value' => $image_url,
					],
				],
			]
		);

		$result = $query->get_posts();

		wp_reset_postdata();

		return 1 !== count( $result ) ? 0 : $result[0];
	}

	/**
	 * Sideloads the game's image into the WordPress media library.
	 *
	 * @TODO Extract this logic into a separate class.
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

		update_post_meta( $img_id, '_bgc_orig_image_url', $image_url );

		return $img_id;
	}

	/**
	 * Set the featured image on a Game post.
	 *
	 * @param int      $post_id Post ID of the game in WordPress.
	 * @param GameData $game    Game data from BoardGameGeek.
	 */
	function set_featured_image( $post_id, GameData $game ) {
		$image_id = $this->get_image_id_from_url( $game->get_image_url() ) ?: $this->load_image( $post_id, $game->get_image_url(), $game->get_name() );

		if ( ! $image_id ) {
			return;
		}

		update_post_meta( $post_id, '_thumbnail_id', $image_id );
	}
}
