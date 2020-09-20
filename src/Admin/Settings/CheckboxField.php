<?php
/**
 * Model for a checkbox field.
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2020-09-13
 * @package JMichaelWard\BoardGameCollector\Admin\Settings
 */

namespace JMichaelWard\BoardGameCollector\Admin\Settings;

/**
 * Class CheckboxField
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2020-09-13
 * @package JMichaelWard\BoardGameCollector\Admin\Settings
 */
class CheckboxField extends Field {
	/**
	 * Render the CheckboxField.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2020-09-13
	 * @return void
	 */
	public function render() {
		$checked = isset( $this->settings->get_data()[ $this->key ] ) ? 'checked ' : '';

		echo '<input type="checkbox" '
			. 'id="' . esc_attr( $this->key ) . '" '
			. 'name="' . esc_attr( SettingsFields::SETTINGS_KEY ) . '[' . esc_attr( $this->key ) . ']" '
			. esc_attr( $checked )
			. '/>';
	}
}
