<?php
/**
 * Plugin Name: Board Game Collector
 * Plugin URI: https://jmichaelward.com
 * Description: Connects to the BoardGameGeek API to retrieve and adapt data for use by WordPress.
 * Author: J. Michael Ward
 * Author URI: https://jmichaelward.com
 *
 * @package JMichaelWard\BoardGameCollector
 */

namespace JMichaelWard\BoardGameCollector;

use JMichaelWard\BoardGameCollector\Admin\Notifier;

$autoload = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

if ( ! file_exists( $autoload ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'src/Admin/Notifier.php';

	add_action( 'admin_notices', [ new Notifier(), 'do_error_message_missing_autoloader' ] );
	return;
}

require_once $autoload;

$plugin = new BoardGameCollector();
$plugin->run();
