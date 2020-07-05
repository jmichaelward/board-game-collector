<?php
namespace JMichaelWard\BoardGameCollector\Admin;

use JMichaelWard\BoardGameCollector\Admin\Settings\SettingsFields;
use JMichaelWard\BoardGameCollector\Admin\Settings\SettingsPage;
use JMichaelWard\OopsWPPlus\Utility\Hydratable;
use WebDevStudios\OopsWP\Structure\Service;
use WebDevStudios\OopsWP\Utility\FilePathDependent;

/**
 * Class Settings
 *
 * @package JMichaelWard\BoardGameCollector
 */
class Settings extends Service implements Hydratable, SettingsFields {
	use FilePathDependent;

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
	 * Settings data.
	 *
	 * @var array $data
	 */
	private $data;

	/**
	 * Settings page hooks.
	 */
	public function register_hooks() {
		add_action( 'admin_init', [ $this, 'setup_settings_pages' ] );
		add_action( 'admin_menu', [ $this, 'init_settings' ] );
		add_action( 'admin_menu', [ $this, 'register_settings_pages' ] );
		add_action( 'admin_notices', [ $this, 'notify_missing_username' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	/**
	 * Hydrate the Settings object with its saved values.
	 */
	public function hydrate() {
		$this->data = get_option( self::SETTINGS_KEY, [] );
	}

	/**
	 * Initialize settings pages.
	 *
	 * @TODO This is a re-use of the service instantiation in the main plugin class. Consider generalizing.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-09-02
	 */
	public function init_settings() {
		$pages = array_map(
			function ( $page_classname ) {
				return [
					'namespace' => $page_classname,
					'object'    => new $page_classname( $this->data ),
				];
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
		foreach ( $this->pages as $page ) {
			$page->set_file_path( plugin_dir_path( $this->file_path ) );
			$page->register();
		}
	}

	/**
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-09-02
	 * @return void
	 */
	public function setup_settings_pages() {
		/** @var SettingsPage $page_class */
		foreach ( $this->pages as $page_class ) {
			$page = new $page_class( $this->data );
			$page->setup();
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

		$script_path = 'app/dist/index.js';
		$js          = plugins_url( $script_path, $this->file_path . 'board-game-collector.php' );

		wp_register_script(
			'bgc-settings-js',
			$js,
			[ 'wp-element' ],
			filemtime( plugin_dir_path( $this->file_path ) . "/{$script_path}" ),
			true
		);

		wp_localize_script( 'bgc-settings-js', 'bgcollector', [
			'apiRoot' => get_site_url( null, '/wp-json/bgc/v1' ),
			'nonce'   => wp_create_nonce( 'wp_rest' ),
		] );

		wp_enqueue_script( 'bgc-settings-js' );
	}

	/**
	 * Get the username from the settings.
	 *
	 * @return string
	 */
	public function get_username() {
		return sanitize_title( $this->data['bgg-username'] ?? '' );
	}


	/**
	 * Render an admin notice only on the bgc_game screen if a BGG Username is not entered.
	 */
	public function notify_missing_username() {
		$screen = get_current_screen();

		if ( 'bgc_game' !== $screen->post_type || $this->has_username() ) {
			return;
		}

		( new Notifier() )->do_warning_settings_not_configured();
	}

	/**
	 * Check whether the username field is entered.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-09-02
	 * @return bool
	 */
	private function has_username() {
		return isset( $this->data[ self::USERNAME_KEY ] ) && ! empty( $this->data[ self::USERNAME_KEY ] );
	}
}
