<?php
/**
 * A text field input.
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2020-09-13
 * @package JMichaelWard\BoardGameCollector\Admin\Settings
 */

namespace JMichaelWard\BoardGameCollector\Admin\Settings;

/**
 * Class TextField
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2020-09-13
 * @package JMichaelWard\BoardGameCollector\Admin\Settings
 */
class TextField extends Field {
	/**
	 * Render the TextField object.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2020-09-13
	 * @return void
	 */
	public function render(): void {
		echo '<input type="text" id="' . esc_attr( $this->key )
		     . '" name="bgc-settings[' . esc_attr( $this->key ) . ']" value="'
		     . esc_attr( $this->settings->get_data()[ $this->key ] ) . '" />';
	}
}
