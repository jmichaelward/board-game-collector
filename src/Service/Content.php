<?php
namespace JMichaelWard\BoardGameCollector\Service;

use JMichaelWard\BoardGameCollector\Content\CPT as CPT;
use JMichaelWard\BoardGameCollector\Content\Taxonomy as Taxonomy;
use JMichaelWard\BoardGameCollector\Hookable;
use JMichaelWard\BoardGameCollector\Service;

/**
 * Class Content
 *
 * @package JMichaelWard\BoardGameCollector
 */
class Content implements Service {
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
	 *
	 */
	public function hooks() {
		$content = array_merge( $this->cpts, $this->taxonomies );

		/* @var $content_type Hookable */
		foreach ( $content as $class ) {
			$content_type = new $class;
			$content_type->hooks();
		}
	}
}
