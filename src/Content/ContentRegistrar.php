<?php
/**
 *
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-04-12
 * @package JMichaelWard\BoardGameCollector\Content
 */

namespace JMichaelWard\BoardGameCollector\Content;

use JMichaelWard\BoardGameCollector\Content\PostType as PostType;
use JMichaelWard\BoardGameCollector\Content\Taxonomy as Taxonomy;
use JMichaelWard\BoardGameCollector\Content\Block as Block;
use WebDevStudios\OopsWP\Structure\Content\ContentTypeInterface;
use WebDevStudios\OopsWP\Structure\Service;
use WebDevStudios\OopsWP\Utility\FilePathDependent;

/**
 * Class ContentRegistrar
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-04-12
 * @package JMichaelWard\BoardGameCollector\Content
 */
class ContentRegistrar extends Service {
	use FilePathDependent;

	/**
	 * Plugin post types.
	 *
	 * @var array
	 * @since 2019-04-12
	 */
	private $post_types = [
		PostType\Game::class,
	];

	/**
	 * Plugin taxonomies.
	 *
	 * @var array
	 * @since 2019-04-12
	 */
	private $taxonomies = [
		Taxonomy\GameStatus::class,
	];

	/**
	 * Custom editor blocks.
	 *
	 * @var array
	 */
	private $editor_blocks = [
		Block\GameBlock::class,
	];

	/**
	 * Register this service's hooks with WordPress.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-04-12
	 */
	public function register_hooks(): void {
		add_action( 'init', [ $this, 'register_post_types' ] );
		add_action( 'init', [ $this, 'register_taxonomies' ] );
		add_action( 'init', [ $this, 'register_editor_blocks' ] );
		add_action( 'init', [ $this, 'add_featured_image_support' ] );
	}

	/**
	 * Register this plugin's post types.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-04-12
	 */
	public function register_post_types(): void {
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
	public function register_taxonomies(): void {
		foreach ( $this->taxonomies as $taxonomy_class ) {
			$taxonomy = new $taxonomy_class();
			$this->register_content_type( $taxonomy );
		}
	}

	/**
	 * Registers custom blocks in the editor.
	 *
	 * @TODO Determine a shared interface for custom editor blocks and refactor.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-12-14
	 * @return void
	 */
	public function register_editor_blocks(): void {
		foreach ( $this->editor_blocks as $editor_block_class ) {
			$editor_block = new $editor_block_class();
			$editor_block->set_file_path( $this->file_path );
			$editor_block->register_hooks();
		}
	}

	/**
	 * Add featured-image support.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-04-12
	 */
	public function add_featured_image_support(): void {
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
	private function register_content_type( ContentTypeInterface $content_type ): void {
		$content_type->register();
	}
}
