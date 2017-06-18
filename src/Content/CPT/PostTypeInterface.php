<?php
namespace JMichaelWard\BoardGameWeekly\Content\CPT;

/**
 * Interface PostTypeInterface
 *
 * @package JMichaelWard\BoardGameWeekly\Content\CPT
 */
interface PostTypeInterface {
	/**
	 * Setup WordPress hooks.
	 *
	 * @return mixed
	 */
	public function hooks();

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
