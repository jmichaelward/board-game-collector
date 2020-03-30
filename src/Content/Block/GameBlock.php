<?php
/**
 * Model for a Game editor block.
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-12-14
 * @package JMichaelWard\BoardGameCollector\Content\Block
 */

namespace JMichaelWard\BoardGameCollector\Content\Block;

use \Exception;

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

	/**
	 * GameBlock constructor.
	 *
	 * @throws Exception If block name is not defined.
	 */
	public function __construct() {
		parent::__construct();
		$this->js_dependencies = array_merge( $this->js_dependencies, [ 'wp-api' ] );
	}
}
