<?php
/**
 *
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2020-09-13
 * @package JMichaelWard\BoardGameCollector\Admin\Settings
 */

namespace JMichaelWard\BoardGameCollector\Admin\Settings;

use JMichaelWard\OopsWPPlus\Utility\Hydratable;
use WebDevStudios\OopsWP\Utility\Hookable;
use WebDevStudios\OopsWP\Utility\Registerable;
use WebDevStudios\OopsWP\Utility\Renderable;

/**
 * Class SettingsInterface
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2020-09-13
 * @package JMichaelWard\BoardGameCollector\Admin\Settings
 */
interface SettingsInterface extends SettingsFields, Hookable, Hydratable, Registerable, Renderable {
	/**
	 * Get the settings data.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2020-09-13
	 * @return mixed
	 */
	public function get_data();
}
