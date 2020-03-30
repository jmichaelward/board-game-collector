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
	protected $block_name = 'single-game';
}
