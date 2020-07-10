<?php
/**
 * Registers a custom settings page with WordPress.
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-05-01
 * @package JMichaelWard\BoardGameCollector\Admin\Settings
 */

namespace JMichaelWard\BoardGameCollector\Admin\Settings;

use JMichaelWard\BoardGameCollector\Admin\Notifier;
use JMichaelWard\BoardGameCollector\Api\BoardGameGeek;
use WebDevStudios\OopsWP\Utility\FilePathDependent;
use WebDevStudios\OopsWP\Utility\Hookable;
use WebDevStudios\OopsWP\Utility\Registerable;
use WebDevStudios\OopsWP\Utility\Renderable;

/**
 * Class Menu
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-05-01
 * @package JMichaelWard\BoardGameCollector\Admin\Settings
 */
class SettingsPage implements SettingsFields, Hookable, Registerable, Renderable {
	use FilePathDependent;

	private const VERIFIED_USERNAME_KEY = 'verified-username';

	/**
	 * Class instance.
	 *
	 * @var Notifier
	 */
	private $notifier;

	/**
	 * BGG API instance.
	 *
	 * @var BoardGameGeek
	 */
	private $bgg_api;

	/**
	 * The Settings data.
	 *
	 * @var array
	 * @since 2019-05-01
	 */
	private $data;

	/**
	 * Form fields.
	 *
	 * @var array
	 */
	private $fields = [];

	public function __construct( BoardGameGeek $bgg_api, Notifier $notifier ) {
		$this->bgg_api  = $bgg_api;
		$this->notifier = $notifier;
	}

	/**
	 * Register hooks for this settings page.
	 */
	public function register_hooks() {
		add_action( 'admin_init', [ $this, 'notify_invalid_username' ] );
		add_action( 'admin_notices', [ $this, 'notify_missing_username' ] );
	}

	/**
	 * Initialize the options fields in the Settings page.
	 *
	 * @return array
	 */
	private function init_fields() : array {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				self::USERNAME_KEY => __( 'BoardGameGeek Username', 'bgc' ),
			];
		}

		return $this->fields;
	}

	/**
	 * Register the settings page.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-05-01
	 * @return void
	 */
	public function register() {
		$this->setup();
		$this->register_hooks();

		add_submenu_page(
			'edit.php?post_type=bgc_game',
			__( 'BGG Settings', 'bgc' ),
			__( 'BGG Settings', 'bgc' ),
			'manage_options',
			self::SETTINGS_KEY,
			[ $this, 'render' ]
		);

		register_setting( self::SETTINGS_KEY, self::SETTINGS_KEY, [] );
	}

	/**
	 * Setup the settings page sections and fields.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-05-01
	 * @return void
	 */
	private function setup() {
		$this->init_fields();
		$this->add_section();
		$this->add_fields();
		$this->data = get_option( self::SETTINGS_KEY, [] );
	}

	/**
	 * Render the settings page.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-05-01
	 * @return void
	 */
	public function render() {
		include $this->file_path . '/views/settings.php';
	}

	/**
	 * Register the settings section.
	 */
	public function add_section() {
		add_settings_section(
			self::SETTINGS_KEY,
			__( 'BoardGameGeek API Settings', 'bgc' ),
			null,
			self::SETTINGS_KEY
		);
	}

	/**
	 * Register settings fields.
	 */
	public function add_fields() {
		foreach ( $this->fields as $id => $name ) {
			add_settings_field(
				$id,
				$name,
				[ $this, 'render_text_input' ],
				self::SETTINGS_KEY,
				self::SETTINGS_KEY,
				[
					'id' => $id,
				]
			);
		}
	}

	/**
	 * Render a text input field.
	 *
	 * @param array $args Fields.
	 */
	public function render_text_input( $args ) {
		echo '<input type="text" id="' . esc_attr( $args['id'] )
			. '" name="bgc-settings[' . esc_attr( $args['id'] ) . ']" value="'
			. esc_attr( $this->data[ $args['id'] ] ) . '" />';
	}

	/**
	 * Validate whether the username saved to the settings page is registered with BoardGameGeek.
	 */
	public function notify_invalid_username() {
		if ( $this->username_verified() || ! $this->is_settings_page() ) {
			return;
		}

		$username = $this->get_username();

		if ( empty( $username ) ) {
			return;
		}

		$is_valid_username = $this->bgg_api->is_username_valid( $username );

		$this->data[ self::VERIFIED_USERNAME_KEY ] = $is_valid_username;
		update_option( self::SETTINGS_KEY, $this->data );

		if ( $is_valid_username ) {
			return;
		}

		$this->notifier->do_error_notice( "There is no user registered on BoardGameGeek by the name of ${username}." );
	}

	/**
	 * Check whether a username as been verified.
	 *
	 * Recently updated settings are always considered non-verified.
	 *
	 * @return bool
	 */
	private function username_verified() : bool {
		if ( filter_input( INPUT_GET, 'settings-updated', FILTER_VALIDATE_BOOLEAN ) ) {
			return false;
		}

		return get_option( self::SETTINGS_KEY )[ self::VERIFIED_USERNAME_KEY ] ?? false;
	}

	/**
	 * Check whether it's our custom settings page.
	 *
	 * @return bool
	 */
	private function is_settings_page() : bool {
		return self::SETTINGS_KEY === filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
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

		$this->notifier->do_warning_settings_not_configured();
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
