<?php
/**
 *
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @since   2019-09-02
 * @package JMichaelWard\BoardGameCollector\Api\Routes
 */

namespace JMichaelWard\BoardGameCollector\Api\Routes;

/**
 * Class CustomRestRoute
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @since   2019-09-02
 * @package JMichaelWard\BoardGameCollector\Api\Routes
 */
abstract class CustomRestRoute extends RestRoute {
	/**
	 * Namespace for all REST routes.
	 *
	 * @var string
	 * @since 2019-09-01
	 */
	protected $namespace = 'bgc/v1';
}
