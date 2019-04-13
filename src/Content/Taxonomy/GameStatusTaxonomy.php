<?php
namespace JMichaelWard\BoardGameCollector\Content\Taxonomy;

use WebDevStudios\OopsWP\Utility\Hookable;

/**
 * Class GameStatusTaxonomy
 *
 * @package JMichaelWard\BoardGameCollector\Content\Taxonomy
 */
class GameStatusTaxonomy implements Hookable {
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
		register_taxonomy(
			'bgc_game_status',
			'bgc_game',
			$this->args()
		);
	}

	/**
	 * Taxonomy labels.
	 *
	 * @return array
	 */
	public function labels() {
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
	public function args() {
		return [
			'label'                 => _x( 'Statuses', 'Game status label', 'bgc' ),
			'labels'                => $this->labels(),
			'show_in_rest'          => true,
			'hierarchical'          => true,
			'rest_base'             => 'ownership',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
		];
	}
}
