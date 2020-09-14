<?php
/**
 * Factory services are those which create objects of their own.
 *
 * This class is used primarily to provide an interface to identify these types
 * of classes in order to take advantage of dependency injection.
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2020-09-13
 * @package JMichaelWard\BoardGameCollector\Utility
 */

namespace JMichaelWard\BoardGameCollector\Utility;

use WebDevStudios\OopsWP\Structure\Service;

/**
 * Class FactoryService
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2020-09-13
 * @package JMichaelWard\BoardGameCollector\Utility
 */
abstract class FactoryService extends Service implements InstantiatorInterface {
	use Instantiator;
}
