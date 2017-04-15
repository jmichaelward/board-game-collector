<?php
/**
 * Plugin Name: BoardGameGeek Data
 * Plugin URI: https://jmichaelward.com
 * Description: Connects to the BoardGameGeek API to retrieve and adapt data for use by WordPress.
 * Author: J. Michael Ward
 * Author URI: https://jmichaelward.com
 *
 * @package BGW\BoardGameGeek
 */

use BGW\BoardGameGeek\BoardGameGeek;

require_once plugin_dir_path( __FILE__ ) . 'src/BoardGameGeek.php';

$plugin = new BoardGameGeek;

add_action( 'plugins_loaded', [ $plugin, 'run' ] );
