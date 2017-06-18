<?php
namespace JMichaelWard\BoardGameWeekly\Content\CPT;

/**
 * Class Game
 *
 * @package JMichaelWard\BoardGameWeekly\Content\CPT;
 */
class GamePostType implements PostTypeInterface {
	/**
	 * Setup WordPress hooks.
	 */
	public function hooks() {
		add_action( 'init', [ $this, 'register' ] );
		add_action( 'init', [ $this, 'add_theme_support' ] );
	}

	/**
	 * Register this post type.
	 */
	public function register() {
		register_post_type( 'bgw_game', $this->args() );
	}

	/**
	 * Labels for this post type.
	 *
	 * @return array
	 */
	public function labels() {
		return [
			'name'               => _x( 'Games', 'post type general name', 'bgw' ),
			'singular_name'      => _x( 'Game', 'post type singular name', 'bgw' ),
			'menu_name'          => _x( 'Games', 'admin menu', 'bgw' ),
			'name_admin_bar'     => _x( 'Game', 'add new on admin bar', 'bgw' ),
			'add_new'            => _x( 'Add New', 'bgw_game', 'bgw' ),
			'add_new_item'       => __( 'Add New Game', 'bgw' ),
			'new_item'           => __( 'New Game', 'bgw' ),
			'edit_item'          => __( 'Edit Game', 'bgw' ),
			'view_item'          => __( 'View Game', 'bgw' ),
			'all_items'          => __( 'All Games', 'bgw' ),
			'search_items'       => __( 'Search Games', 'bgw' ),
			'parent_item_colon'  => __( 'Parent Games:', 'bgw' ),
			'not_found'          => __( 'No games found', 'bgw' ),
			'not_found_in_trash' => __( 'No games found in Trash.', 'bgw' ),
		];
	}

	/**
	 * Arguments for post type registration.
	 *
	 * @return array
	 */
	public function args() {
		return [
			'label'                 => _x( 'Games', 'post type label', 'bgw' ),
			'labels'                => $this->labels(),
			'description'           => __( 'A post type for a board games collection', 'bgw' ),
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
		add_theme_support( 'post-thumbnails', [ 'bgw_game' ] );
	}
}
