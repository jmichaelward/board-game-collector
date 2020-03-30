<?php
/**
 * Abstract representation of an editor block.
 *
 * @package JMichaelWard\BoardGameCollector\Content\Block
 */

namespace JMichaelWard\BoardGameCollector\Content\Block;

use WebDevStudios\OopsWP\Utility\FilePathDependent;
use WebDevStudios\OopsWP\Utility\Hookable;
use \Exception;

/**
 * Class EditorBlock
 *
 * @package JMichaelWard\BoardGameCollector\Content\Block
 */
abstract class EditorBlock implements Hookable {
	use FilePathDependent;

	/**
	 * JavaScript dependencies.
	 *
	 * @var array
	 */
	protected $js_dependencies = [
		'wp-blocks',
		'wp-components',
		'wp-editor',
		'wp-element',
	];

	/**
	 * Name of the block.
	 *
	 * @var string
	 */
	protected $block_name;

	/**
	 * Namespace prefix for all custom blocks provided by this plugin.
	 *
	 * @var string
	 */
	protected $prefix = 'board-game-collector';

	/**
	 * EditorBlock constructor.
	 *
	 * @throws Exception If a block name is not defined.
	 */
	public function __construct() {
		if ( ! $this->block_name ) {
			throw new Exception( 'You must define a name for this new block.' );
		}
	}

	/**
	 * Register this block's hooks with WordPress.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-12-14
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_assets' ] );
	}

	/**
	 * Register this block's assets with WordPress.
	 */
	public function enqueue_assets() {
		wp_enqueue_script( // @codingStandardsIgnoreLine
			"{$this->get_script_handle()}-js",
			plugins_url( "app/dist/blocks/{$this->block_name}/index.js", $this->file_path ),
			$this->js_dependencies,
		);
	}

	/**
	 * Get the full name of a block.
	 *
	 * @return string
	 */
	public function get_full_block_name() {
		return "{$this->prefix}/{$this->block_name}";
	}

	/**
	 * Get the handle for a block script.
	 *
	 * This method converts the full block name into a usable script handle.
	 *
	 * Example: board-game-collector/single-game becomes board-game-collector-single-game.
	 *
	 * @return string
	 */
	private function get_script_handle() {
		return str_replace( '/', '-', $this->get_full_block_name() );
	}
}
