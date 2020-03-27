<?php
/**
 * Taxonomy for the game's ownership status.
 *
 * @package JMichaelWard\BoardGameCollector\Content\Taxonomy
 */

namespace JMichaelWard\BoardGameCollector\Content\Taxonomy;

use WebDevStudios\OopsWP\Structure\Content\Taxonomy;

/**
 * Class GameStatus
 *
 * @package JMichaelWard\BoardGameCollector\Content\Taxonomy
 */
class GameStatus extends Taxonomy {
	/**
	 * Taxonomy slug.
	 *
	 * @var string
	 */
	protected $slug = 'bgc_game_status';

	/**
	 * Object types this taxonomy supports.
	 *
	 * @var array
	 */
	protected $object_types = [ 'bgc_game' ];


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
			'hierarchical'          => true,
			'label'                 => _x( 'Statuses', 'Game status label', 'bgc' ),
			'labels'                => $this->get_labels(),
			'rest_base'             => 'ownership',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'show_admin_column'     => true,
			'show_in_rest'          => true,
		];
	}
}
