<?php
namespace JMichaelWard\BoardGameCollector\Admin;

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
	 * Form fields.
	 *
	 * @var array
	 */
	private $fields = [];

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
		add_action( 'admin_init', [ $this, 'register' ] );
		add_action( 'admin_menu', [ $this, 'create_admin_page' ] );
		add_action( 'admin_init', [ $this, 'add_section' ] );
		add_action( 'admin_init', [ $this, 'add_fields' ] );
		add_action( 'admin_notices', [ $this, 'notify_missing_username' ] );
	}

	/**
	 * Register the settings with WordPress.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-05-01
	 * @return void
	 */
	public function register() {
		$this->fields = [
			'bgg-username' => __( 'BoardGameGeek Username', 'bgc' ),
		];

		$this->data = get_option( 'bgc-settings' );
		register_setting( 'bgc-settings', 'bgc-settings', [] );
	}

	/**
	 * Menu page construction.
	 */
	public function create_admin_page() {
		add_submenu_page(
			'edit.php?post_type=bgc_game',
			__( 'BGG Settings', 'bgc' ),
			__( 'BGG Settings', 'bgc' ),
			'manage_options',
			'bgc-settings',
			[ $this, 'admin_callback' ]
		);
	}

	/**
	 * Register the settings section.
	 */
	public function add_section() {
		add_settings_section(
			'bgc-settings',
			'BoardGameGeek API Settings',
			null,
			'bgc-settings'
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
				'bgc-settings',
				'bgc-settings',
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
	 * Get the view file for the settings form.
	 */
	public function admin_callback() {
		include $this->file_path . 'app/views/settings.php'; // @codingStandardsIgnoreLine
	}

	/**
	 * Getter function for retrieving settings data outside of object.
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->data ?? get_option( 'bgc-settings' );
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
