<?php
/**
 * Collection of settings fields constants to provide access to implementing classes.
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package JMichaelWard\BoardGameCollector\Admin\Settings
 * @since 2019-09-02
 */

namespace JMichaelWard\BoardGameCollector\Admin\Settings;

/**
 * Interface SettingsFields
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @since   2019-09-02
 * @package JMichaelWard\BoardGameCollector\Admin\Settings
 */
interface SettingsFields {
	/**
	 * The main settings key.
	 */
	const SETTINGS_KEY = 'bgc-settings';

	/**
	 * The username field.
	 */
	const USERNAME_KEY = 'bgg-username';
}
