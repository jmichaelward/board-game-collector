<?php
namespace JMichaelWard\BoardGameCollector\Admin;

use JMichaelWard\BoardGameCollector\BoardGameCollector;

/**
 * Class Settings
 *
 * @package JMichaelWard\BoardGameCollector
 */
class Settings {
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
	 * Settings constructor.
	 */
	public function __construct() {
		$this->fields = [
			'bgg-username' => __( 'BoardGameGeek Username', 'bgc' ),
		];

		$this->data = get_option( 'bgc-settings' );
	}

	/**
	 * Settings page hooks.
	 */
	public function hooks() {
		add_action( 'admin_menu', [ $this, 'create_admin_page' ] );
		add_action( 'admin_init', [ $this, 'add_section' ] );
		add_action( 'admin_init', [ $this, 'add_fields' ] );
		add_action( 'admin_init', [ $this, 'register' ] );
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
	 * Register the settings.
	 */
	public function register() {
		register_setting( 'bgc-settings', 'bgc-settings', [] );
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
		include BoardGameCollector::app_path() . '/views/settings.php'; // @codingStandardsIgnoreLine
	}

	/**
	 * Getter function for retrieving settings data outside of object.
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->data;
	}
}
