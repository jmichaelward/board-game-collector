<?php
/**
 * Model for a Game editor block.
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-12-14
 * @package JMichaelWard\BoardGameCollector\Content\Block
 */

namespace JMichaelWard\BoardGameCollector\Content\Block;

/**
 * Class GameBlock
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-12-14
 * @package JMichaelWard\BoardGameCollector\Content\Block
 */
class GameBlock extends EditorBlock {
	/**
	 * The dirname for this block asset.
	 *
	 * @var string
	 */
	private $dirname = 'single-game';

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
			'bgc_game_block',
			plugins_url( "app/dist/blocks/{$this->dirname}/index.js", $this->file_path ),
			[ 'wp-blocks', 'wp-element' ]
		);
	}
}
