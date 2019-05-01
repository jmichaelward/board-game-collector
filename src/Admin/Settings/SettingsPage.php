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
class SettingsPage implements Renderable {
	use FilePathDependent;

	/**
	 * The slug for the settings page.
	 *
	 * @var string
	 * @since 2019-05-01
	 */
	private $slug;

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
	public function __construct( string $slug, array $data ) {
		$this->slug = $slug;
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
			'bgg-username' => __( 'BoardGameGeek Username', 'bgc' ),
		];

		add_submenu_page(
			'edit.php?post_type=bgc_game',
			__( 'BGG Settings', 'bgc' ),
			__( 'BGG Settings', 'bgc' ),
			'manage_options',
			$this->slug,
			[ $this, 'render' ]
		);
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

	/**
	 * Register the settings section.
	 */
	public function add_section() {
		add_settings_section(
			$this->slug,
			__( 'BoardGameGeek API Settings', 'bgc' ),
			null,
			$this->slug
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
				$this->slug,
				$this->slug,
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
