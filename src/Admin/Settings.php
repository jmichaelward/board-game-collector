<?php
namespace JMichaelWard\BoardGameCollector\Admin;

use JMichaelWard\BoardGameCollector\Admin\Settings\SettingsFields;
use JMichaelWard\BoardGameCollector\Admin\Settings\SettingsPage;
use JMichaelWard\BoardGameCollector\Utility\FactoryService;
use WebDevStudios\OopsWP\Utility\FilePathDependent;

/**
 * Class Settings
 *
 * @package JMichaelWard\BoardGameCollector
 */
class Settings extends FactoryService implements SettingsFields {
	use FilePathDependent;

	/**
	 * WordPress handle for the Settings JavaScript.
	 */
	private const JS_SETTINGS_NAME = 'bgc-settings-js';

	/**
	 * The settings menu class.
	 *
	 * @var SettingsPage
	 * @since 2019-05-01
	 */
	private $pages = [
		SettingsPage::class,
	];

	/**
	 * Settings page hooks.
	 */
	public function register_hooks() {
		add_action( 'admin_menu', [ $this, 'register_settings_pages' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	/**
	 * Initialize settings pages.
	 *
	 * @TODO This is a re-use of the service instantiation in the main plugin class. Consider generalizing.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-09-02
	 */
	private function initialize_settings_pages() {
		$pages = array_map(
			function ( $page_classname ) {
				return $this->create( $page_classname, true );
			},
			$this->pages
		);

		$this->pages = array_column( $pages, 'object', 'namespace' );
	}

	/**
	 * Register this plugin's settings pages.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-09-02
	 * @return void
	 */
	public function register_settings_pages() {
		$this->initialize_settings_pages();

		foreach ( $this->pages as $page ) {
			$page->set_file_path( plugin_dir_path( $this->file_path ) );
			$page->register();
		}
	}

	/**
	 * Enqueue the plugin assets.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-08-30
	 * @return void
	 */
	public function enqueue_assets( $hook ) {
		if ( 'bgc_game_page_bgc-settings' !== $hook ) {
			return;
		}

		$script_path = "{$this->get_js_path()}/settings.js";
		$js          = plugins_url( $script_path, $this->file_path . 'board-game-collector.php' );

		wp_enqueue_script(
			self::JS_SETTINGS_NAME,
			$js,
			[ 'wp-element', 'wp-api-fetch' ],
			filemtime( plugin_dir_path( $this->file_path ) . "/{$script_path}" ),
			true
		);

		wp_localize_script(
			self::JS_SETTINGS_NAME,
			'bgcollector',
			[
				'apiRoot' => get_site_url( null, '/wp-json/bgc/v1' ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
			]
		);
	}

    private function get_js_path(): string {
        return is_readable( plugin_dir_path( $this->file_path ) . 'app/dist/js' )
            ? 'app/dist/js/'
            : 'app/src/js/';
    }
}
