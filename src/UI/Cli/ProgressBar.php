<?php
/**
 * Registers a ProgressBar for use by WP-CLI.
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-05-01
 * @package JMichaelWard\BoardGameCollector\UI\Cli
 */

namespace JMichaelWard\BoardGameCollector\UI\Cli;

use cli\progress\Bar;
use WebDevStudios\OopsWP\Structure\Service;
use function WP_CLI\Utils\make_progress_bar;

/**
 * Class ProgressBar
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-05-01
 * @package JMichaelWard\BoardGameCollector\UI\Cli
 */
class ProgressBar extends Service {
	/**
	 * The WP-CLI progress bar object.
	 *
	 * @var Bar
	 * @since 2019-05-01
	 */
	private $bar;

	/**
	 * Number of records.
	 *
	 * @var int
	 * @since 2019-05-01
	 */
	private $count;

	/**
	 * Register the hooks for the Progress Bar.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-05-01
	 */
	public function register_hooks() {
		add_action( 'bgc_setup_progress_bar', [ $this, 'init' ], 10, 1 );
		add_action( 'bgc_tick_progress_bar', [ $this, 'increment' ] );
		add_action( 'bgc_finish_progress_bar', [ $this, 'finish' ] );
	}

	/**
	 * Initialize the progress bar.
	 *
	 * @param int $count Number of items upon which to indicate progress.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-05-01
	 * @return void
	 */
	public function init( int $count ) {
		$this->count = $count;
		$this->bar   = make_progress_bar( "Processing {$this->count} games...", $this->count );
	}

	/**
	 * Increment the progress bar.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-05-01
	 * @return void
	 */
	public function increment() {
		$this->bar->tick();
	}

	/**
	 * Finish and reset the progress bar.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-05-01
	 * @return void
	 */
	public function finish() {
		$this->bar->finish();
		$this->bar->reset();
	}
}
