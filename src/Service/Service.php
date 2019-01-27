<?php
namespace JMichaelWard\BoardGameCollector\Service;

use JMichaelWard\BoardGameCollector\Hookable;

/**
 * Class Service
 *
 * @package JMichaelWard\BoardGameCollector\Service
 */
abstract class Service implements Hookable {
	/**
	 * Register the service.
	 */
	public function register() {
		$this->register_hooks();
	}
}
