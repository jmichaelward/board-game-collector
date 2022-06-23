<?php
/**
 * Plugin Name: Board Game Collector
 * Plugin URI: https://jmichaelward.com
 * Description: Import your data from BoardGameGeek.com into WordPress!
 * Author: J. Michael Ward
 * Author URI: https://jmichaelward.com
 * Version: 0.1.0
 *
 * @package JMichaelWard\BoardGameCollector
 */

namespace JMichaelWard\BoardGameCollector;

use Auryn\Injector;
use DI\Container;
use JMichaelWard\BoardGameCollector\Admin\Notifier;

try {
	$autoload = __DIR__ . '/vendor/autoload.php';

	if ( is_readable( $autoload ) ) {
		require_once $autoload;
	}

	add_action( 'plugins_loaded', [ new BoardGameCollector( __FILE__, new Container() ), 'run' ] );
} catch ( \Throwable $e ) {
	require_once __DIR__ . '/src/Admin/Notifier.php';

	( new Notifier() )->do_error_notice(
		__( 'Could not locate BoardGameCollector class. Did you remember to run composer install?', 'bgcollector' )
	);
}

