<?php
use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

defined( 'ABSPATH' ) || die;

/**
 * Install plugin dependencies if they are not already present in the application.
 *
 * @param string $plugin_class Fully-qualified name of the main plugin class.
 */
function maybe_install_dependencies( string $plugin_class ) {
	try {
		$plugin_dir = dirname( __DIR__ );

		// Extract our composer.phar into the vendor directory.
		$composer_path = "{$plugin_dir}/bin/extractedComposer";

		if ( ! is_readable( $composer_path ) ) {
			$composer = new \Phar( "{$plugin_dir}/bin/composer.phar" );
			$composer->extractTo( $composer_path );
		}

		require_once "{$composer_path}/vendor/autoload.php";

		// Create the installer command.
		$input = new ArrayInput( [ 'command' => 'install', '-d' => "{$plugin_dir}", '--no-dev' => false ] );

		// Run the installer.
		$application = new Application();

		// Symfony Console normally auto-exits, which causes WordPress installation to fail, so setting it false is key.
		$application->setAutoExit( false );
		$application->run( $input, new NullOutput() );
	} catch ( \Throwable $e ) {
		error_log( $e->getMessage() );
	}
}

/**
 * Require a plugin-level autoloader if one exists.
 */
function maybe_autoload() {
	$autoload = dirname( __DIR__ ) . '/vendor/autoload.php';

	if ( ! is_readable( $autoload ) ) {
		return;
	}

	require_once $autoload;
}
