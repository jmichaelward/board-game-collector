<?php
/**
 * Interface for transformed data from BoardGameGeek.
 *
 * @package JMichaelWard\BoardGameCollector\Model\Games
 */

namespace JMichaelWard\BoardGameCollector\Model\Games;

/**
 * Interface GameData
 *
 * @package JMichaelWard\BoardGameCollector\Model\Games
 */
interface GameData {
	/**
	 * ID of the game.
	 *
	 * @return int
	 */
	public function get_bgg_id();

	/**
	 * Box title of the game.
	 *
	 * @return string
	 */
	public function get_name();

	/**
	 * URL path to a screenshot of the game's box art.
	 *
	 * @return string
	 */
	public function get_image_url();

	/**
	 * Minimum number of players this game requires to play.
	 *
	 * @return int
	 */
	public function get_min_players();

	/**
	 * Maximum number of players this game will support.
	 *
	 * @return int
	 */
	public function get_max_players();

	/**
	 * Whether the game is owned by the user.
	 *
	 * @return bool
	 */
	public function is_owned();

	/**
	 * Get the statuses of the game (e.g., owned, wishlist, and so on).
	 *
	 * @return array
	 */
	public function get_statuses();

	/**
	 * Get the unique identifiers for the game for use by WP_Query.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-04-13
	 * @return mixed
	 */
	public function get_unique_identifiers();
}
