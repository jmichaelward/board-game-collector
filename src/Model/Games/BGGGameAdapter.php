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
		return new BGGGame();
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
		return $this->data['stats']['rating']['ranks']['rank'] ?? [];
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
	private function image_url() : string {
		return $this->data['image'] ?? '';
	}


	// Data from BGG to convert:
	// - name
	// - year published
	// - image url
	// - thumbnail url
	// - stats { @attributes { minplayers, maxplayers } }
	// - rating { @attributes { value } }
	// - numplays

	// Structure we will want:
	// - name
	// - publish_year
	// - image { full, thumbnail }
	// - player_count { min, max }
	// - rating
	// - play_count

	/*
	{
	"name": "King of New York",
	"yearpublished": "2014",
	"image": "http://api.bgwkly.dev/wp-content/uploads/2017/07/pic2407103.jpg",
	"thumbnail": "http://api.bgwkly.dev/wp-content/uploads/2017/07/pic2407103-300x300.jpg",
	"stats": {
	"@attributes": {
	"minplayers": "2",
	"maxplayers": "6",
	"minplaytime": "40",
	"maxplaytime": "40",
	"playingtime": "40",
	"numowned": "14148"
	},
	"rating": {
		"@attributes": {
			"value": "6"
        },
        "usersrated": {
			"@attributes": {
				"value": "8811"
            }
        },
        "average": {
		"@attributes": {
			"value": "7.18038"
		}
    },
    "bayesaverage": {
		"@attributes": {
			"value": "6.98434"
		}
    },
    "stddev": {
		"@attributes": {
			"value": "1.25366"
          }
	},
    "median": {
	"@attributes": {
		"value": "0"
	}
},
"ranks": {
			"rank": [
	            {
		            "@attributes": {
		            "type": "subtype",
	                "id": "1",
	                "name": "boardgame",
	                "friendlyname": "Board Game Rank",
	                "value": "339",
	                "bayesaverage": "6.98434"
	              }
	            },
	            {
		            "@attributes": {
		            "type": "family",
	                "id": "5499",
	                "name": "familygames",
	                "friendlyname": "Family Game Rank",
	                "value": "59",
	                "bayesaverage": "7.01974"
	              }
	            }
	          ]
	        }
	      }
	    },
	    "status": {
		"@attributes": {
			"own": "1",
	        "prevowned": "0",
	        "fortrade": "0",
	        "want": "0",
	        "wanttoplay": "0",
	        "wanttobuy": "0",
	        "wishlist": "0",
	        "preordered": "0",
	        "lastmodified": "2016-05-15 14:14:43"
	      }
	    },
	    "numplays": "0"
	  },
		*/
}
