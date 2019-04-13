<?php
namespace JMichaelWard\BoardGameCollector\Content\CPT;

use WebDevStudios\OopsWP\Utility\Hookable;

/**
 * Class Game
 *
 * @package JMichaelWard\BoardGameCollector\Content\CPT;
 */
class GamePostType implements PostTypeInterface, Hookable {
	/**
	 * Setup WordPress hooks.
	 */
	public function register_hooks() {
		add_action( 'init', [ $this, 'register' ] );
		add_action( 'init', [ $this, 'add_theme_support' ] );
	}

	/**
	 * Register this post type.
	 */
	public function register() {
		register_post_type( 'bgc_game', $this->args() );
	}

	/**
	 * Labels for this post type.
	 *
	 * @return array
	 */
	public function labels() {
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
	public function args() {
		return [
			'label'                 => _x( 'Games', 'post type label', 'bgc' ),
			'labels'                => $this->labels(),
			'description'           => __( 'A post type for a board games collection', 'bgc' ),
			'public'                => false,
			'publicly_queryable'    => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'rewrite'               => [
				'slug' => 'game',
			],
			'capability_type'       => 'post',
			'has_archive'           => false,
			'hierarchical'          => false,
			'supports'              => [ 'title', 'editor', 'thumbnail' ],
			'show_in_rest'          => true,
			'rest_base'             => 'games',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		];
	}

	/**
	 * Add theme support for this post type.
	 */
	public function add_theme_support() {
		add_theme_support( 'post-thumbnails', [ 'bgc_game' ] );
	}
}
