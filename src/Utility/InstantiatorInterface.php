<?php
/**
 * Interface for objects which instantiate other objects.
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @package JMichaelWard\BoardGameCollector\Utility * @since 2020-09-13
 */

namespace JMichaelWard\BoardGameCollector\Utility;

use DI\Container;

interface InstantiatorInterface {
	/**
	 * Sets the Container instance on an object.
	 *
	 * @param Container $container Container instance.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2020-09-13
	 */
	public function set_container( Container $container );
}
