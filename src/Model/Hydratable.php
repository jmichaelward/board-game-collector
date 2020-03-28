<?php
/**
 * Interface for objects which require property initialization.
 *
 * @package JMichaelWard\BoardGameCollector\Model
 */

namespace JMichaelWard\BoardGameCollector\Model;

/**
 * Interface Hydratable
 *
 * @package JMichaelWard\BoardGameCollector\Model
 */
interface Hydratable {
	/**
	 * Hydrate the object's properties with data.
	 */
	public function hydrate();
}
