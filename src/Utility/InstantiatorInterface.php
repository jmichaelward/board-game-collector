<?php
/**
 * Interface for objects which instantiate other objects.
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @package JMichaelWard\BoardGameCollector\Utility * @since 2020-09-13
 */

namespace JMichaelWard\BoardGameCollector\Utility;

use Auryn\Injector;

interface InstantiatorInterface {
	/**
	 * Sets the Injector instance on an object.
	 *
	 * @param Injector $injector Injector instance.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2020-09-13
	 */
	public function set_injector( Injector $injector );
}
