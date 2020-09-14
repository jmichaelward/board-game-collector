<?php
/**
 * Trait for objects which require the ability to instantiate other objects.
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @package JMichaelWard\BoardGameCollector\Utility * @since 2020-09-13
 */

namespace JMichaelWard\BoardGameCollector\Utility;

use Auryn\Injector;

/**
 * Trait Instantiator
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2020-09-13
 * @package JMichaelWard\BoardGameCollector\Utility
 */
trait Instantiator {
	/**
	 * Instance of the Auryn\Injector object.
	 *
	 * @var Injector
	 */
	protected $injector;

	/**
	 * Set the Auryn\Injector instance on an object.
	 *
	 * @param Injector $injector Injector instance.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2020-09-13
	 */
	public function set_injector( Injector $injector ) {
		$this->injector = $injector;
	}
}
