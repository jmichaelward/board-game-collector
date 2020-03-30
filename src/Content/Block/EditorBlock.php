<?php
/**
 * Abstract representation of an editor block.
 *
 * @package JMichaelWard\BoardGameCollector\Content\Block
 */

namespace JMichaelWard\BoardGameCollector\Content\Block;

use WebDevStudios\OopsWP\Utility\FilePathDependent;
use WebDevStudios\OopsWP\Utility\Hookable;

/**
 * Class EditorBlock
 *
 * @package JMichaelWard\BoardGameCollector\Content\Block
 */
abstract class EditorBlock implements Hookable {
	use FilePathDependent;

	/**
	 * Namespace prefix for all custom blocks provided by this plugin.
	 *
	 * @var string
	 */
	protected $prefix = 'board-game-collector';
}
