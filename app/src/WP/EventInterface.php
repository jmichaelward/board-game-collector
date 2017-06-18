<?php
namespace JMichaelWard\BoardGameCollector\WP;

/**
 * Interface EventInterface
 *
 * @package JMichaelWard\BoardGameCollector\WP
 */
interface EventInterface {
	/**
	 * Register actions and filters with WordPress.
	 *
	 * @return mixed
	 */
	public function hooks();
}
