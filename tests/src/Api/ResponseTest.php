<?php
/**
 * Response class tests.
 */

namespace JMichaelWard\BoardGameCollector\Tests\Api;

use JMichaelWard\BoardGameCollector\Api\Response;
use WP_Mock;
use WP_Mock\Tools\TestCase;

/**
 * Class ResponseTest
 *
 * @package JMichaelWard\BoardGameCollector\Tests\Api
 */
class ResponseTest extends TestCase {
	/**
	 * Example XML response from BoardGameGeek.
	 *
	 * @return string
	 */
	public function get_mock_single_game() {
		return <<<BGGXML
<?xml version="1.0" encoding="utf-8" standalone="yes"?><items totalitems="221" termsofuse="https://boardgamegeek.com/xmlapi/termsofuse" pubdate="Fri, 27 Mar 2020 15:45:35 +0000">
	<item objecttype="thing" objectid="27708" subtype="boardgame" collid="40411094">
		<name sortindex="1">1960: The Making of the President</name>
		<yearpublished>2007</yearpublished>			<image>https://cf.geekdo-images.com/original/img/V2ZLDmIl-5JA0_C10ZmDRkd_PfA=/0x0/pic3499166.jpg</image>
		<thumbnail>https://cf.geekdo-images.com/thumb/img/O1ZXfeK3VwoKTHSgJa2nBSLxuuk=/fit-in/200x150/pic3499166.jpg</thumbnail>
		<stats minplayers="2"																	maxplayers="2"																	minplaytime="90"																	maxplaytime="120"																	playingtime="120"																	numowned="9630" >
			<rating value="N/A">			<usersrated value="8208" />			<average value="7.59612" />
				<bayesaverage value="7.26945" />			<stddev value="1.3494" />
				<median value="0" />			<ranks>
					<rank type="subtype" id="1" name="boardgame" friendlyname="Board Game Rank" value="186" bayesaverage="7.26945" />
					<rank type="family" id="5497" name="strategygames" friendlyname="Strategy Game Rank" value="128" bayesaverage="7.33539" />
				</ranks>		</rating>
		</stats>	<status own="0" prevowned="0" fortrade="0" want="0" wanttoplay="1" wanttobuy="0" wishlist="0"  preordered="0" lastmodified="2017-02-09 09:28:24" />
		<numplays>0</numplays></item></items>
BGGXML;
	}

	/**
	 * @test
	 */
	public function get_body_converts_xml_to_json() {
		$mock_response = [
			'body' => $this->get_mock_single_game()
		];

		$response = new Response( $mock_response );

		WP_Mock::userFunction( 'wp_remote_retrieve_body', [
			'return' => $mock_response['body']
		] );

		$this->assertArrayHasKey( 'item', $response->get_body() );
	}

	/**
	 * @test
	 */
	public function get_status_code_returns_200() {
		$response_data = [
			'response' => [
				'code' => 200,
				'message' => 'OK',
			]
		];

		$response = new Response( $response_data );

		WP_Mock::userFunction( 'wp_remote_retrieve_response_code', [
			'return' => 200,
		] );

		$this->assertSame( 200, $response->get_status_code() );
	}
}

