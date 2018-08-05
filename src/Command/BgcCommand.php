<?php
namespace JMichaelWard\BoardGameCollector\Command;

/**
 * Command entrypoint for the Board Game Collector plugin.
 *
 * ## EXAMPLES
 *
 *     # Update Games in the WordPress database.
 *     $ wp bgg update
 *     Success: Games posts created in WordPress.
 *
 * @when after_wp_config_load
 */
class BgcCommand extends \WP_CLI_Command {
	/**
	 * Update the Games post type with data from BoardGameGeek.
	 */
	public function update() {

	}
}

