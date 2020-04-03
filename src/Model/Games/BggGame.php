<?php
/**
 * Data model for BoardGameGeek game data.
 *
 * @package JMichaelWard\BoardGameCollector\Model\Games
 */

namespace JMichaelWard\BoardGameCollector\Model\Games;

/**
 * Class BGGGame
 *
 * @package JMichaelWard\BoardGameCollector\Model\Games
 */
class BggGame implements GameData {
	/**
	 * BoardGameCollector ID for this game.
	 *
	 * @var int
	 */
	private $bgg_id;

	/**
	 * Box title of the game.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Year the game was published.
	 *
	 * @var string
	 * @since 2019-04-13
	 */
	private $year_published;

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
	private $play_attributes;

	/**
	 * Game rankings.
	 *
	 * @var array
	 */
	private $rankings;

	/**
	 * BGGGame constructor.
	 *
	 * @param int    $bgg_id          The ID of the game from BoardGameGeek.
	 * @param string $name            The name of the game.
	 * @param string $year_published  The year the game was published.
	 * @param string $image_url       A URL to an image of the box art.
	 * @param array  $play_attributes Attributes related to the game such as number of players and playtime.
	 * @param array  $rankings        The game's rankings on BoardGameGeek.
	 * @param array  $status          The player's ownership status for the game.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-04-13
	 */
	public function __construct(
		int $bgg_id,
		string $name,
		string $year_published = '',
		string $image_url = '',
		array $play_attributes = [],
		array $rankings = [],
		array $status = []
	) {
		$this->bgg_id          = $bgg_id;
		$this->name            = $name;
		$this->year_published  = $year_published;
		$this->image_url       = $image_url;
		$this->play_attributes = $play_attributes;
		$this->rankings        = $rankings;
		$this->status          = $status;
	}

	/**
	 * Get the object data as an array.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-05-01
	 * @return array
	 */
	public function get_data() : array {
		return get_object_vars( $this );
	}

	/**
	 * ID of the game.
	 *
	 * @return int
	 */
	public function get_bgg_id() {
		return absint( $this->bgg_id );
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
		return absint( $this->play_attributes['minplayers'] );
	}

	/**
	 * Maximum number of players this game will support.
	 *
	 * @return int
	 */
	public function get_max_players() {
		return absint( $this->play_attributes['maxplayers'] );
	}

	/**
	 * Whether the game is owned by the user.
	 *
	 * @return bool
	 */
	public function is_owned() {
		return 1 === absint( $this->status['own'] );
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

	/**
	 * Data that uniquely identifies this game in WordPress.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-04-13
	 * @return array
	 */
	public function get_unique_identifiers() : array {
		return [
			'name'       => sanitize_title( $this->name ),
			'post_title' => $this->name,
			'meta_query' => [ // @codingStandardsIgnoreLine
				[
					'key'     => 'bgc_game_id',
					'value'   => $this->bgg_id,
					'compare' => '=',
				],
			],
		];
	}
}
