<?php
namespace JMichaelWard\BoardGameCollector\Model\Games;

/**
 * Class BGGGame
 *
 * @package JMichaelWard\BoardGameCollector\Model\Games
 */
class BGGGame implements GameData {
	/**
	 * Data returned from BoardGameCollector API request.
	 *
	 * @var array
	 */
	private $data;

	/**
	 * BoardGameCollector ID for this game.
	 *
	 * @var int
	 */
	private $id;

	/**
	 * Box title of the game.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * URL to an image of the box art.
	 *
	 * @var string
	 */
	private $image_url;

	/**
	 * Ownership attributes.
	 *
	 * @var array
	 */
	private $status;

	/**
	 * Game's player information.
	 *
	 * E.g., minimum number of players, maximum number of players.
	 *
	 * @var array
	 */
	private $player_info;

	/**
	 * User rating for the game.
	 *
	 * @var int
	 */
	private $rating;

	/**
	 * BGGGame constructor.
	 *
	 * @param array $data Game data from BoardGameCollector.
	 */
	public function __construct( array $data ) {
		if ( ! $data ) {
			return;
		}

		$this->data        = $data;
		$this->id          = $data['@attributes']['objectid'] ?? 0;
		$this->name        = $data['name'] ?? '';
		$this->image_url   = $data['image'] ?? '';
		$this->status      = $data['status']['@attributes'] ?? [];
		$this->player_info = $data['stats']['@attributes'] ?? [];
		$this->rating      = $data['rating']['@attributes']['value'] ?? 0;
	}

	/**
	 * Get the full set of data from the game.
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * ID of the game.
	 *
	 * @return int
	 */
	public function get_id() {
		return absint( $this->id );
	}

	/**
	 * Box title of the game.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * URL path to a screenshot of the game's box art.
	 *
	 * @return string
	 */
	public function get_image_url() {
		return $this->image_url;
	}

	/**
	 * Minimum number of players this game requires to play.
	 *
	 * @return int
	 */
	public function get_min_players() {
		return absint( $this->player_info['minplayers'] );
	}

	/**
	 * Maximum number of players this game will support.
	 *
	 * @return int
	 */
	public function get_max_players() {
		return absint( $this->player_info['maxplayers'] );
	}

	/**
	 * The user's numeric rating of the game.
	 *
	 * @return int
	 */
	public function get_user_rating() {
		return 'N/A' !== $this->rating ? absint( $this->rating ) : 0;
	}

	/**
	 * Whether the game is owned by the user.
	 *
	 * @return bool
	 */
	public function is_owned() {
		return absint( $this->status['own'] ) === 1;
	}

	/**
	 * Get the statuses for the games (e.g., owned, wishlist ).
	 *
	 * @return array
	 */
	public function get_statuses() {
		$statuses = array_filter(
			$this->status,
			function( $status, $key ) {
				if ( in_array( $key, [ 'own', 'preordered', 'wanttoplay' ], true ) && '1' === $status ) {
					return true;
				}

				if ( 'wishlist' === $key && '0' !== $status ) {
					return true;
				}

				return false;
			},
			ARRAY_FILTER_USE_BOTH
		);

		return array_keys( $statuses );
	}
}
