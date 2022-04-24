<?php

use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

defined( 'ABSPATH' ) || die;

/**
 * Install plugin dependencies if they are not already present in the application.
 *
 * @param string $plugin_filepath The plugin file path.
 * @param string $plugin_class    Fully-qualified name of the main plugin class.
 */
function maybe_install_dependencies( string $plugin_filepath, string $plugin_class ) {
	// Plugin class is already being autoloaded.
	if ( class_exists( $plugin_class ) ) {
		return;
	}

	try {
		$plugin_dir = pathinfo( $plugin_filepath, PATHINFO_DIRNAME );

		// Extract the included composer.phar into the vendor directory.
		$composer_path = "{$plugin_dir}/bin/extractedComposer";
		$composer_phar = "{$plugin_dir}/bin/composer.phar";

		if ( ! is_readable( $composer_path ) && is_executable( $composer_phar ) ) {
			( new \Phar( $composer_phar ) )->extractTo( $composer_path );
		}

		if ( ! file_exists( "{$composer_path}/vendor/autoload.php" ) ) {
			return;
		}

		require_once "{$composer_path}/vendor/autoload.php";

		// Create the installer command.
		$input = new ArrayInput(
			[
				'command'  => 'install',
				'-d'       => "{$plugin_dir}",
				'-o'       => true,
				'--no-dev' => true,
			]
		);

		// Run the installer.
		$application = new Application();

		// Prevent Symfony Console from auto-exiting, as it causes WordPress installation to fail.
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
