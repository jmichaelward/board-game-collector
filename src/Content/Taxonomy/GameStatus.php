<?php
namespace JMichaelWard\BoardGameCollector\Content\Taxonomy;

use WebDevStudios\OopsWP\Structure\Content\Taxonomy;

/**
 * Class GameStatus
 *
 * @package JMichaelWard\BoardGameCollector\Content\Taxonomy
 */
class GameStatus extends Taxonomy {
	/**
	 * Setup WordPress hooks.
	 */
	public function register_hooks() {
		add_action( 'init', [ $this, 'register' ] );
	}

	/**
	 * Register the taxonomy.
	 */
	public function register() {
		register_taxonomy( 'bgc_game_status', 'bgc_game', $this->get_args() );
	}

	/**
	 * Taxonomy labels.
	 *
	 * @return array
	 */
	public function get_labels() : array {
		return [
			'name'          => _x( 'Ownership', 'Game status label', 'bgc' ),
			'singular_name' => _x( 'Ownership', 'Game status singular label', 'bgc' ),
		];
	}

	/**
	 * Arguments for taxonomy registration.
	 *
	 * @return array
	 */
	public function get_args() : array {
		return [
			'label'                 => _x( 'Statuses', 'Game status label', 'bgc' ),
			'labels'                => $this->get_labels(),
			'show_in_rest'          => true,
			'hierarchical'          => true,
			'rest_base'             => 'ownership',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
		];
	}
}
