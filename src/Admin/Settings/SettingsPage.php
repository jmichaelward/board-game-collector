<?php
/**
 *
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @since   2019-05-01
 * @package JMichaelWard\BoardGameCollector\Admin\Settings
 */

namespace JMichaelWard\BoardGameCollector\Admin\Settings;

use WebDevStudios\OopsWP\Utility\FilePathDependent;
use WebDevStudios\OopsWP\Utility\Renderable;

/**
 * Class Menu
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @since   2019-05-01
 * @package JMichaelWard\BoardGameCollector\Admin\Settings
 */
class SettingsPage implements SettingsFields, Renderable {
	use FilePathDependent;

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

	/**
	 * SettingsPage constructor.
	 *
	 * @param string $slug The slug for the SettingsPage.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-05-01
	 */
	public function __construct( array $data ) {
		$this->data = $data;
	}

	/**
	 * Register the settings page.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-05-01
	 * @return void
	 */
	public function register() {
		$this->fields = [
			self::USERNAME_KEY => __( 'BoardGameGeek Username', 'bgc' ),
		];

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
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-05-01
	 * @return void
	 */
	public function setup() {
		$this->add_section();
		$this->add_fields();
	}

	/**
	 * Render the settings page.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-05-01
	 * @return void
	 */
	public function render() {
		include $this->file_path . 'app/views/settings.php';
	}

	public function load() {
		$this->setup();
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
}
