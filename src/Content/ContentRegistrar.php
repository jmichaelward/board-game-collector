<?php
/**
 *
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-04-12
 * @package JMichaelWard\BoardGameCollector\Content
 */

namespace JMichaelWard\BoardGameCollector\Content;

use JMichaelWard\BoardGameCollector\Content\CPT\GamePostType;
use JMichaelWard\BoardGameCollector\Content\Taxonomy\GameStatus;
use WebDevStudios\OopsWP\Structure\Content\ContentTypeInterface;
use WebDevStudios\OopsWP\Structure\Service;

/**
 * Class ContentRegistrar
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-04-12
 * @package JMichaelWard\BoardGameCollector\Content
 */
class ContentRegistrar extends Service {
	/**
	 * Plugin post types.
	 *
	 * @var array
	 * @since 2019-04-12
	 */
	private $post_types = [
		GamePostType::class,
	];

	/**
	 * Plugin taxonomies.
	 *
	 * @var array
	 * @since 2019-04-12
	 */
	private $taxonomies = [
		GameStatus::class,
	];

	/**
	 * Register this service's hooks with WordPress.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-04-12
	 */
	public function register_hooks() {
		add_action( 'init', [ $this, 'register_post_types' ] );
		add_action( 'init', [ $this, 'register_taxonomies' ] );
		add_action( 'init', [ $this, 'add_featured_image_support' ] );
	}

	/**
	 * Register this plugin's post types.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-04-12
	 */
	public function register_post_types() {
		foreach ( $this->post_types as $post_type_class ) {
			$post_type = new $post_type_class();
			$this->register_content_type( $post_type );
		}
	}

	/**
	 * Register this plugin's taxonomies.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-04-12
	 */
	public function register_taxonomies() {
		foreach ( $this->taxonomies as $taxonomy_class ) {
			$taxonomy = new $taxonomy_class();
			$this->register_content_type( $taxonomy );
		}
	}

	/**
	 * Add featured-image support.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-04-12
	 */
	public function add_featured_image_support() {
		add_theme_support( 'post-thumbnails' );
	}

	/**
	 * Register a content type.
	 *
	 * @param ContentTypeInterface $content_type ContentType instance.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-04-12
	 * @return void
	 */
	private function register_content_type( ContentTypeInterface $content_type ) {
		$content_type->register();
	}
}
