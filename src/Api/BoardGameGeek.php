<?php
/**
 * Model for the BoardGameGeek API.
 *
 * @TODO Consider whether this is the right location for this class.
 *
 * @package JMichaelWard\BoardGameCollector\Api
 */

namespace JMichaelWard\BoardGameCollector\Api;

use JMichaelWard\BoardGameCollector\Cron\CronService;
use InvalidArgumentException;
use Exception;

/**
 * Class BoardGameGeek
 *
 * @package JMichaelWard\BoardGameCollector\Api
 */
class BoardGameGeek {
	/**
	 * The name of the collection transient key.
	 */
	public const COLLECTION_TRANSIENT_KEY = 'bgg_collection';

	/**
	 * Base path for the BoardGameGeek API.
	 *
	 * @var string
	 */
	private $base_path = 'https://www.boardgamegeek.com/xmlapi2';

	/**
	 * Attempt to retrieve a user's game collection from the API.
	 *
	 * @param string $username The user collection to retrieve.
	 * @throws Exception|InvalidArgumentException If API error response or missing username.
	 *
	 * @return Response
	 */
	public function request_user_collection( string $username ) : Response {
		$cached = get_transient( self::COLLECTION_TRANSIENT_KEY );

		if ( $cached ) {
			return $cached;
		}

		$response = $this->request_games_by_username( $username );

		if ( 200 == $response->get_status_code() && $response->get_body()['item'] ?? [] ) {
			set_transient( self::COLLECTION_TRANSIENT_KEY, $response, CronService::INTERVAL_VALUE );
		}

		return $response;
	}

	/**
	 * Get games from the BoardGameGeek API.
	 *
	 * @param string $username The username to query against.
	 *
	 * @throws Exception If API error response.
	 * @throws InvalidArgumentException If missing username.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-04-12
	 * @return Response
	 */
	private function request_games_by_username( string $username ) : Response {
		if ( ! $username ) {
			throw new InvalidArgumentException(
				__( 'No username set in BGC Settings. Refusing to make request.', 'bgcollector' )
			);
		}

		$request  = new Request( "{$this->base_path}/collection?username={$username}&stats=1" );

		return $request->make();
	}
}
