<?php
namespace JMichaelWard\BoardGameCollector\Command;

/**
 * Class BggCommand
 *
 * @package JMichaelWard\BoardGameCollector\Command
 */
class BggCommand extends BaseCommand {
	/**
	 * Name of the command.
	 *
	 * @var string
	 */
	public static $name = 'bgg';

	/**
	 * Static getter for the command name.
	 *
	 * @return string
	 */
	public static function get_name() {
		return self::$name;
	}

	/**
	 * Run commands against the BoardGameGeek API.
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Command associative arguments.
	 */
	public function __invoke( $args, $assoc_args ) {
		\WP_CLI::success( 'You invoked the command!' );
	}
}

