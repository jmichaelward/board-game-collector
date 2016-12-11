<?php
namespace BGW\BoardGameGeek;

class GamesUpdater {
    /**
     *
     */
    public function get_data() {
        if ( ! $data = get_transient( 'bgg_collection' ) ) {
            $curl = curl_init();

            curl_setopt( $curl, CURLOPT_URL, 'https://bgg-json.azurewebsites.net/collection/thegermwar' );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );

            $data = json_decode( curl_exec( $curl ) );

            set_transient( 'bgg_collection', $data, 5 * MINUTE_IN_SECONDS );

            curl_close( $curl );
        }

        return $data;
    }

    /**
     *
     */
    public function update_collection() {
        // Load required WordPress functionality.
        include_once ABSPATH . WPINC . '/pluggable.php';
        wp_set_auth_cookie( 1 );

        if ( ! function_exists( 'media_handle_sideload' ) ) {
            include_once ABSPATH . '/wp-admin/includes/image.php';
            include_once ABSPATH . '/wp-admin/includes/file.php';
            include_once ABSPATH . '/wp-admin/includes/media.php';
        }

        foreach ( $this->get_data() as $game ) {
            if ( ! $game->isExpansion && ! $this->game_exists( $game ) ) {
                $this->insert_game( $game );
            }
        }
    }

    /**
     * @param $game
     *
     * @return bool
     */
    private function game_exists( $game ) {
        $args = [
            'name'           => $game->gameId,
            'post_title'     => $game->name,
            'post_type'      => 'bgw_game',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
        ];

        if ( get_posts( $args ) ) {
            return true;
        }

        return false;
    }

    /**
     * @param $game
     */
    private function insert_game( $game ) {
        $args = [
            'post_type'   => 'bgw_game',
            'post_name'   => $game->gameId,
            'post_title'  => $game->name,
            'post_status' => 'publish',
        ];

        $id = wp_insert_post( $args );

        if ( $id ) {
            $this->load_image( $id, $game->image );
        }

        /*
        gameID
        name
        image
        thumbnail
        minPlayers
        maxPlayers
        playingTime
        isExpansion
        yearPublished
        bggRating
        averageRating
        rank
        numPlays
        owned
        preOrdered
        forTrade
        previousOwned
        want
        wantToPlay
        wantToBuy
        wishList
        userComment
        */

        return true;
    }

    private function load_image( $id, $image_url ) {
        $tmp = download_url( $image_url );

        if ( is_wp_error( $tmp ) ) {
            return false;
        }

        $file_array = [];

        // Set variables for storage
        // fix file filename for query strings
        preg_match( '/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $image_url, $matches );
        $file_array['name']     = basename( $matches[0] );
        $file_array['tmp_name'] = $tmp;

        // If error storing temporarily, unlink
        if ( is_wp_error( $tmp ) ) {
            @unlink( $file_array['tmp_name'] );
            $file_array['tmp_name'] = '';
        }

        $img = media_handle_sideload( $file_array, $id, '' );

        // If error storing permanently, unlink
        if ( is_wp_error( $id ) ) {
            @unlink( $file_array['tmp_name'] );

            return $id;
        }
        $src = wp_get_attachment_url( $id );
    }
}