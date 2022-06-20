<?php

use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

defined( 'ABSPATH' ) || die;

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
