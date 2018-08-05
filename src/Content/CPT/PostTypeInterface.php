<?php
namespace JMichaelWard\BoardGameCollector\Content\CPT;

/**
 * Interface PostTypeInterface
 *
 * @package JMichaelWard\BoardGameCollector\Content\CPT
 */
interface PostTypeInterface {
	/**
	 * Register the post type.
	 *
	 * @return mixed
	 */
	public function register();

	/**
	 * Labels for the post type.
	 *
	 * @return array
	 */
	public function labels();

	/**
	 * Arguments for registering the post type.
	 *
	 * @return array
	 */
	public function args();
}
