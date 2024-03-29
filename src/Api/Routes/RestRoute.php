<?php
/**
 * Abstract class for REST routes which extend native WordPress.
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-09-01
 * @package JMichaelWard\BoardGameCollector\Api\Routes
 */

namespace JMichaelWard\BoardGameCollector\Api\Routes;

use WebDevStudios\OopsWP\Utility\Registerable;

/**
 * Class RestRoute
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-09-01
 * @package JMichaelWard\BoardGameCollector\Api\Routes
 */
abstract class RestRoute extends \WP_REST_Controller implements Registerable {
	/**
	 * Register the RestRoute with WordPress.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-09-02
	 * @return void
	 */
	public function register(): void {
		$this->register_routes();
	}
}
