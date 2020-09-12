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

require_once __DIR__ . '/src/functions.php';

$plugin_class = 'JMichaelWard\\BoardGameCollector\\BoardGameCollector';


/**
 * Install plugin dependencies on activation.
 */
register_activation_hook(
	__FILE__,
	function() use ( $plugin_class ) {
		maybe_install_dependencies( $plugin_class );
	}
);


maybe_autoload();

if ( ! class_exists( $plugin_class ) ) {
	require_once __DIR__ . '/src/Admin/Notifier.php';

	( new Notifier() )->do_error_notice(
		'Could not locate BoardGameCollector class. Did you remember to run composer install?'
	);

	return;
}

add_action( 'plugins_loaded', [ new BoardGameCollector( __FILE__, new Injector() ), 'run' ] );
