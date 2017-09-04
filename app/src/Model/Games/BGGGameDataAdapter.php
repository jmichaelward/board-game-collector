<?php
namespace JMichaelWard\BoardGameCollector\Model\Games;

/**
 * Class BGGGameDataAdapter
 *
 * Converts the data structure from BoardGameGeek into a custom structure for the API.
 *
 * @package JMichaelWard\BoardGameCollector\Model\Games
 */
class BGGGameDataAdapter {
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
