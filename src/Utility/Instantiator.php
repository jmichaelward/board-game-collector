<?php
/**
 * Trait for objects which require the ability to instantiate other objects.
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @package JMichaelWard\BoardGameCollector\Utility * @since 2020-09-13
 */

namespace JMichaelWard\BoardGameCollector\Utility;

use Auryn\InjectionException;
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

	/**
	 * Create an object instance and return it with references to its fully-qualified namespace.
	 *
	 * @param string $class_name Class name of the object to create.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2020-09-13
	 * @return array
	 */
	protected function create( string $class_name ) : array {
		try {
			return [
				'namespace' => $class_name,
				'object'    => $this->injector->make( $class_name ),
			];
		} catch ( InjectionException $e ) {
			return [];
		}
	}
}
