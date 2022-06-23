<?php
/**
 * Trait for objects which require the ability to instantiate other objects.
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @package JMichaelWard\BoardGameCollector\Utility * @since 2020-09-13
 */

namespace JMichaelWard\BoardGameCollector\Utility;

use DI\Container;

/**
 * Trait Instantiator
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2020-09-13
 * @package JMichaelWard\BoardGameCollector\Utility
 */
trait Instantiator {
	/**
	 * Container instance.
	 *
	 * @var Container
	 */
	protected Container $injector;

	/**
	 * Set the Container instance on an object.
	 *
	 * @param Container $injector Container instance.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2020-09-13
	 */
	public function set_injector( Container $injector ) {
		$this->injector = $injector;
	}

	/**
	 * Create an object instance.
	 *
	 * @param string $class_name         Class name of the object to create.
	 * @param bool   $contained_in_array Whether to retrieve the object contained in an array.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2020-09-13
	 * @return object|array
	 */
	protected function create( string $class_name, bool $contained_in_array = false ) {
		try {
			$object = $this->injector->get( $class_name );

			if ( ! $contained_in_array ) {
				return $object;
			}

			return [
				'namespace' => $class_name,
				'object'    => $object,
			];
		} catch ( InjectionException $e ) {
			return [];
		}
	}
}
