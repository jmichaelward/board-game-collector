<?php
namespace JMichaelWard\BoardGameWeekly\Content\Taxonomy;

/**
 * Class GameStatusTaxonomy
 *
 * @package JMichaelWard\BoardGameWeekly\Content\Taxonomy
 */
class GameStatusTaxonomy {
	/**
	 * Setup WordPress hooks.
	 */
	public function hooks() {
		add_action( 'init', [ $this, 'register' ] );
	}

	/**
	 * Register the taxonomy.
	 */
	public function register() {
		register_taxonomy(
			'bgw_game_status',
			'bgw_game',
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
			'name'          => _x( 'Ownership', 'Game status label', 'bgw' ),
			'singular_name' => _x( 'Ownership', 'Game status singular label', 'bgw' ),
		];
	}

	/**
	 * Arguments for taxonomy registration.
	 *
	 * @return array
	 */
	public function args() {
		return [
			'label'                 => _x( 'Statuses', 'Game status label', 'bgw' ),
			'labels'                => $this->labels(),
			'show_in_rest'          => true,
			'hierarchical'          => true,
			'rest_base'             => 'ownership',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
		];
	}
}
