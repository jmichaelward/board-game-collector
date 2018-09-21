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

$autoload = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

if ( is_readable( $autoload ) ) {
	require_once $autoload;
}

if ( ! class_exists( 'JMichaelWard\\BoardGameCollector\\BoardGameCollector' ) ) {
	include plugin_dir_path( __FILE__ ) . '/app/src/Admin/Notifier.php';

	add_action( 'admin_notices', [
		new JMichaelWard\BoardGameCollector\Admin\Notifier(),
		'do_error_message_missing_autoloader',
	] );

	// Deactivate the plugin.
	add_action( 'admin_init', function() {
		deactivate_plugins( __FILE__ );
	});

	return;
}

$plugin = new \JMichaelWard\BoardGameCollector\BoardGameCollector();
$plugin->run();
