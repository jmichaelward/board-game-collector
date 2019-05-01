<?php
namespace JMichaelWard\BoardGameCollector\Content\PostType;

use WebDevStudios\OopsWP\Structure\Content\PostType;

/**
 * Class Game
 *
 * @author Jeremy Ward <jeremy@jmichaelward.com>
 * @package JMichaelWard\BoardGameCollector\Content\PostType;
 */
class Game extends PostType {
	/**
	 * The slug for this post type.
	 *
	 * @var string
	 * @since 2019-05-01
	 */
	protected $slug = 'bgc_game';

	/**
	 * Labels for this post type.
	 *
	 * @return array
	 */
	public function get_labels() : array {
		return [
			'name'               => _x( 'Games', 'post type general name', 'bgc' ),
			'singular_name'      => _x( 'Game', 'post type singular name', 'bgc' ),
			'menu_name'          => _x( 'Games', 'admin menu', 'bgc' ),
			'name_admin_bar'     => _x( 'Game', 'add new on admin bar', 'bgc' ),
			'add_new'            => _x( 'Add New', 'bgc_game', 'bgc' ),
			'add_new_item'       => __( 'Add New Game', 'bgc' ),
			'new_item'           => __( 'New Game', 'bgc' ),
			'edit_item'          => __( 'Edit Game', 'bgc' ),
			'view_item'          => __( 'View Game', 'bgc' ),
			'all_items'          => __( 'All Games', 'bgc' ),
			'search_items'       => __( 'Search Games', 'bgc' ),
			'parent_item_colon'  => __( 'Parent Games:', 'bgc' ),
			'not_found'          => __( 'No games found', 'bgc' ),
			'not_found_in_trash' => __( 'No games found in Trash.', 'bgc' ),
		];
	}

	/**
	 * Arguments for post type registration.
	 *
	 * @return array
	 */
	public function get_args() : array {
		return [
			'label'                 => _x( 'Games', 'post type label', 'bgc' ),
			'description'           => __( 'A post type for a board games collection', 'bgc' ),
			'rewrite'               => [
				'slug' => 'games',
			],
			'capability_type'       => 'post',
			'capabilities'          => [
				'create_posts' => 'do_not_allow',
			],
			'map_meta_cap'          => true,
			'has_archive'           => true,
			'hierarchical'          => false,
			'supports'              => [ 'title', 'editor', 'thumbnail' ],
			'show_in_rest'          => true,
			'rest_base'             => 'games',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		];
	}
}
