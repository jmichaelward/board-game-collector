<?php
/**
 * Plugin Name: Board Game Collector
 * Plugin URI: https://jmichaelward.com
 * Description: Connects to the BoardGameGeek API to retrieve and adapt data for use by WordPress.
 * Author: J. Michael Ward
 * Author URI: https://jmichaelward.com
 * Version: 0.1.0
 *
 * @package JMichaelWard\BoardGameCollector
 */

namespace JMichaelWard\BoardGameCollector;

use Auryn\Injector;
use JMichaelWard\BoardGameCollector\Admin\Notifier;
use Throwable;

$plugin_path = plugin_dir_path( __FILE__ );
$autoload    = $plugin_path . 'vendor/autoload.php';

if ( is_readable( $autoload ) ) {
	require_once $autoload;
}

try {
	add_action( 'plugins_loaded', [ new BoardGameCollector( __FILE__, new Injector() ), 'run' ] );
} catch ( Throwable $e ) {
	require_once $plugin_path . 'src/Admin/Notifier.php';

	( new Notifier() )->do_error_notice(
		'Could not locate BoardGameCollector class. Did you remember to run composer install?'
	);

	// Deactivate the plugin.
	add_action(
		'admin_init',
		function () {
			deactivate_plugins( __FILE__ );
		}
	);
}
