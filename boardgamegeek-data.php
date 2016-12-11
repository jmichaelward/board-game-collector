<?php
/**
 * Plugin Name: BoardGameGeek Data
 * Plugin URI: https://boardgamesweek.com
 * Description: Connects to the Board Games Geek API to retrieve and adapt data for use by the BGW API.
 * Author: J. Michael Ward
 * Author URI: https://jmichaelward.com
 */

use BGW\BoardGameGeek\BoardGameGeek;

require_once plugin_dir_path( __FILE__ ) . 'src/BoardGameGeek.php';

$plugin = new BoardGameGeek;
$plugin->run();
