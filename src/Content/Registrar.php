<?php

namespace JMichaelWard\BoardGameCollector\Content;

use JMichaelWard\BoardGameCollector\Content\CPT as CPT;
use JMichaelWard\BoardGameCollector\Content\Taxonomy as Taxonomy;

/**
 * Class Registrar
 *
 * @package JMichaelWard\BoardGameCollector
 */
class Registrar {
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

		/* @var $content_type \JMichaelWard\BoardGameCollector\WP\EventInterface WordPress Event Interface. */
		foreach ( $content as $class ) {
			$content_type = new $class;
			$content_type->hooks();
		}
	}
}
