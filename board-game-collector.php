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
use JMichaelWard\BoardGameCollector\Admin\Notifier;

try {
	require_once __DIR__ . '/src/functions.php';

	maybe_autoload();

	add_action( 'plugins_loaded', [ new BoardGameCollector( __FILE__, new Injector() ), 'run' ] );
} catch ( \Throwable $e ) {
	require_once __DIR__ . '/src/Admin/Notifier.php';

	( new Notifier() )->do_error_notice(
		__( 'Could not locate BoardGameCollector class. Did you remember to run composer install?', 'bgcollector' )
	);
}

