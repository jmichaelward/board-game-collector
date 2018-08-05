<?php
namespace JMichaelWard\BoardGameCollector;

/**
 * Interface Hookable
 *
 * @package JMichaelWard\BoardGameCollector
 */
interface Hookable {
	/**
	 * Register the current Hookable.
	 *
	 * @return void
	 */
	public function register_hooks();
}
