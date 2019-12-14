<?php
/**
 *
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @since   2019-12-14
 * @package JMichaelWard\BoardGameCollector\Content\Block
 */

namespace JMichaelWard\BoardGameCollector\Content\Block;

use WebDevStudios\OopsWP\Utility\FilePathDependent;
use WebDevStudios\OopsWP\Utility\Hookable;

/**
 * Class GameBlock
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
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
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-12-14
	 * @return void
	 */
	public function register_hooks() {
		$this->enqueue_assets();
	}

	public function enqueue_assets() {
		wp_enqueue_script(
			'bgc_game_block',
			plugins_url( "app/dist/blocks/{$this->dirname}/index.js", $this->file_path ),
			[ 'wp-blocks', 'wp-element' ]
		);
	}
}
