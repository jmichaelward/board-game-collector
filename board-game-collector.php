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

use Auryn\Injector;
use JMichaelWard\BoardGameCollector\Admin\Notifier;

$plugin_path = plugin_dir_path( __FILE__ );
$autoload    = $plugin_path . 'vendor/autoload.php';

if ( ! is_readable( $autoload ) ) {
	require_once $plugin_path . 'src/Admin/Notifier.php';

	add_action( 'admin_notices', [ new Notifier(), 'do_error_message_missing_autoloader' ] );

	// Deactivate the plugin.
	add_action(
		'admin_init',
		function () {
			deactivate_plugins( __FILE__ );
		}
	);

	return;
}

require_once $autoload;

$plugin = new BoardGameCollector( $plugin_path, new Injector() );
$plugin->run();
