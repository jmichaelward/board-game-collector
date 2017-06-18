<?php
namespace BGW\BoardGameGeek\Model\Games;

/**
 * Interface GameDataInterface
 *
 * @package BGW\BoardGameGeek\Model\Games
 */
interface GameDataInterface {
	/**
	 * ID of the game.
	 *
	 * @return int
	 */
	public function get_id();

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
	 * The user's numeric rating of the game.
	 *
	 * @return int
	 */
	public function get_user_rating();

	/**
	 * Whether the game is owned by the user.
	 *
	 * @return bool
	 */
	public function is_owned();
}
