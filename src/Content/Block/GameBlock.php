<?php
/**
 * Model for a Game editor block.
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-12-14
 * @package JMichaelWard\BoardGameCollector\Content\Block
 */

namespace JMichaelWard\BoardGameCollector\Content\Block;

use WebDevStudios\OopsWP\Utility\FilePathDependent;
use WebDevStudios\OopsWP\Utility\Hookable;

/**
 * Class GameBlock
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-12-14
 * @package JMichaelWard\BoardGameCollector\Content\Block
 */
class GameBlock implements Hookable {
	use FilePathDependent;

	/**
	 * The dirname for this block asset.
	 *
	 * @var string
	 */
	private $dirname = 'game';

	/**
	 * Register this block's hooks with WordPress.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-12-14
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'init', $this->enqueue_assets() );
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
