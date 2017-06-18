<?php
/**
 * Plugin Name: BoardGameWeekly Data
 * Plugin URI: https://jmichaelward.com
 * Description: Connects to the BoardGameWeekly API to retrieve and adapt data for use by WordPress.
 * Author: J. Michael Ward
 * Author URI: https://jmichaelward.com
 *
 * @package JMichaelWard\BoardGameWeekly
 */

namespace JMichaelWard\BoardGameWeekly;

use JMichaelWard\BoardGameWeekly\Admin\Notifier;

$autoload = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

if ( ! file_exists( $autoload ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'src/Admin/Notifier.php';

	add_action( 'admin_notices', [ new Notifier(), 'do_error_message_missing_autoloader' ] );
	return;
}

require_once $autoload;

$plugin = new BoardGameWeekly();
$plugin->run();
