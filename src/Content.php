<?php

namespace JMichaelWard\BoardGameWeekly;

use JMichaelWard\BoardGameWeekly\Content\CPT as CPT;
use JMichaelWard\BoardGameWeekly\Content\Taxonomy as Taxonomy;

/**
 * Class Content
 *
 * @package JMichaelWard\BoardGameWeekly
 */
class Content {
	/**
	 * Collection of post types.
	 *
	 * @var array
	 */
	public $cpts = [
		CPT\GamePostType::class,
	];

	/**
	 * Collection of taxonomies.
	 *
	 * @var array
	 */
	public $taxonomies = [
		Taxonomy\GameStatusTaxonomy::class,
	];

	/**
	 * Content constructor.
	 */
	public function __construct() {
		$content = array_merge( $this->cpts, $this->taxonomies );

		foreach ( $content as $class ) {
			$content_type = new $class;
			$content_type->hooks();
		}
	}
}
