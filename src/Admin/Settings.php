<?php
namespace JMichaelWard\BoardGameCollector\Admin;

use JMichaelWard\BoardGameCollector\Admin\Settings\SettingsPage;
use WebDevStudios\OopsWP\Structure\Service;
use WebDevStudios\OopsWP\Utility\FilePathDependent;
use WebDevStudios\OopsWP\Utility\Registerable;

/**
 * Class Settings
 *
 * @package JMichaelWard\BoardGameCollector
 */
class Settings extends Service implements Registerable {
	use FilePathDependent;

	/**
	 * Slug for the settings page.
	 *
	 * @var string
	 * @since 2019-05-01
	 */
	private $slug = 'bgc-settings';

	/**
	 * The settings menu class.
	 *
	 * @var SettingsPage
	 * @since 2019-05-01
	 */
	private $page = SettingsPage::class;

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
		add_action( 'admin_menu', [ $this->page, 'register' ] );
		add_action( 'admin_init', [ $this, 'register' ] );
		add_action( 'admin_notices', [ $this, 'notify_missing_username' ] );
	}

	/**
	 * Run the service.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-05-01
	 * @return void
	 */
	public function run() {
		$this->data = $this->get_data();
		$this->page = new $this->page( $this->slug, $this->data );

		$this->page->set_file_path( $this->file_path );

		parent::run();
	}

	/**
	 * Register the settings with WordPress.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-05-01
	 * @return void
	 */
	public function register() {
		register_setting( $this->slug, $this->slug, [] );

		$this->page->setup();
	}

	/**
	 * Getter function for retrieving settings data outside of object.
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->data ?? get_option( $this->slug );
	}


	/**
	 * Render an admin notice only on the bgc_game screen if a BGG Username is not entered.
	 */
	public function notify_missing_username() {
		$screen       = get_current_screen();
		$has_username = isset( $this->data['bgg-username'] ) && ! empty( $this->data['bgg-username'] );

		if ( 'bgc_game' !== $screen->post_type || $has_username ) {
			return;
		}

		( new Notifier() )->do_warning_settings_not_configured();
	}
}
