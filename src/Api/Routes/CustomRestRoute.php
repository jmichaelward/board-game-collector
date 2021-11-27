<?php
/**
 * Parent abstract class for Custom REST routes.
 *
 * The distinction between this and the standard RestRoute class is
 * that this is the parent class for all bgc-namespaced routes. In contrast,
 * RestRoute is used for modified extensions of native WordPress routes, e.g.,
 * for post types and taxonomies.
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-09-02
 * @see     \JMichaelWard\BoardGameCollector\Api\Routes\RestRoute
 *
 * @package JMichaelWard\BoardGameCollector\Api\Routes
 */

namespace JMichaelWard\BoardGameCollector\Api\Routes;

use WebDevStudios\OopsWP\Utility\Registerable;
use WP_REST_Request;

/**
 * Class CustomRestRoute
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-09-02
 * @package JMichaelWard\BoardGameCollector\Api\Routes
 */
abstract class CustomRestRoute extends \WP_REST_Controller implements Registerable {
	/**
	 * Namespace for all REST routes.
	 *
	 * @var string
	 * @since 2019-09-01
	 */
	protected $namespace = 'bgc/v1';

	/**
	 * Register the RestRoute with WordPress.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2020-04-02
	 * @return void
	 */
	public function register(): void {
		$this->register_routes();
	}

	/**
	 * Basic nonce-based permission callback.
	 *
	 * @param WP_REST_Request $request Class instance.
	 *
	 * @return bool|int
	 */
	public function permission_callback_verify_nonce( WP_REST_Request $request ) {
		return wp_verify_nonce( $request->get_header( 'x_wp_nonce' ), 'wp_rest' );
	}
}
