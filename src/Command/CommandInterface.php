<?php
namespace JMichaelWard\BoardGameCollector\Command;

interface CommandInterface {
	/**
	 * Get the name of a command.
	 *
	 * @return string
	 */
	public static function get_name();
}
