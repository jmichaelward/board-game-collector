<?php
namespace JMichaelWard\BoardGameCollector\Command;

/**
 * Class BaseCommand
 *
 * @package JMichaelWard\BoardGameCollector\Command
 */
abstract class BaseCommand extends \WP_CLI_Command implements CommandInterface {
	/**
	 * Name of the command.
	 *
	 * @var string
	 */
	public static $name;
}
