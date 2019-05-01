<?php
namespace JMichaelWard\BoardGameCollector\Model\Games;

/**
 * Class BGGGameAdapter
 *
 * Converts the data structure from BoardGameGeek into a custom structure for the API.
 *
 * @package JMichaelWard\BoardGameCollector\Model\Games
 */
class BGGGameAdapter {
	/**
	 * Raw data from the BoardGameGeek API.
	 *
	 * @var array
	 * @since 2019-04-13
	 */
	private $data;

	/**
	 * The BoardGameGeek object ID.
	 *
	 * @var int
	 * @since 2019-04-13
	 */
	private $id;

	/**
	 * Name of the game.
	 *
	 * @var string
	 * @since 2019-04-13
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
	 * The game's play attributes.
	 *
	 * E.g., min/max players, playing time.
	 *
	 * @var array
	 * @since 2019-04-13
	 */
	private $play_attributes;

	/**
	 * The game's ranking data on BGG.
	 *
	 * @var array
	 * @since 2019-04-13
	 */
	private $rankings;

	/**
	 * The player's statuses set on the game.
	 *
	 * @var array
	 * @since 2019-04-13
	 */
	private $status;

	/**
	 * The game's image URL.
	 *
	 * @var string
	 * @since 2019-04-13
	 */
	private $image_url;

	/**
	 * BGGGameDataAdapter constructor.
	 *
	 * @param array $data Data from the BoardGameGeek API.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-04-13
	 */
	public function __construct( array $data ) {
		$this->data = $data;
		$this->hydrate();
	}

	/**
	 * Get a BGGGame object from the raw data.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-04-13
	 * @return BGGGame
	 */
	public function get_game() : BGGGame {
		return new BGGGame(
			$this->id,
			$this->name,
			$this->year_published,
			$this->image_url,
			$this->play_attributes,
			$this->rankings,
			$this->status
		);
	}

	/**
	 * Hydrate this object with data.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-04-13
	 * @return void
	 */
	private function hydrate() {
		$vars = array_filter(
			get_object_vars( $this ),
			function ( $key ) {
				return 'data' !== $key;
			},
			ARRAY_FILTER_USE_KEY
		);

		array_walk( array_keys( $vars ), [ $this, 'set' ] );
	}

	/**
	 * Set the value of a property.
	 *
	 * @param string $property The object property.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-04-13
	 * @return BGGGameAdapter
	 */
	private function set( string $property ) {
		$callback          = "parse_{$property}";
		$this->{$property} = $this->$callback();

		return $this;
	}

	/**
	 * Parse the BGG ID.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-04-13
	 * @return int
	 */
	private function parse_id() : ?int {
		return $this->data['@attributes']['objectid'] ?? 0;
	}

	/**
	 * Parse the game name.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-04-13
	 * @return string
	 */
	private function parse_name() : ?string {
		return $this->data['name'] ?? '';
	}

	/**
	 * Parse the game's publication year.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-04-13
	 * @return string
	 */
	private function parse_year_published() : ?string {
		return $this->data['yearpublished'] ?? '';
	}

	/**
	 * Parse the game's play attributes.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-04-13
	 * @return array
	 */
	private function parse_play_attributes() : ?array {
		return $this->data['stats']['@attributes'] ?? [];
	}

	/**
	 * Parse the game's BGG rankings.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-04-13
	 * @return array
	 */
	private function parse_rankings() : array {
		$rankings = [];

		foreach ( $this->data['stats']['rating']['ranks']['rank'] as $ranking ) {
			$rankings[] = $ranking['@attributes'];
		}

		return $rankings;
	}

	/**
	 * Parse the player's statuses for the game.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-04-13
	 * @return array
	 */
	private function parse_status() : array {
		return $this->data['status']['@attributes'] ?? [];
	}

	/**
	 * Parse the game's image URL.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-04-13
	 * @return string
	 */
	private function parse_image_url() : string {
		return $this->data['image'] ?? '';
	}
}
