<?php
/**
 * Collection of settings fields constants to provide access to implementing classes.
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @package JMichaelWard\BoardGameCollector\Admin\Settings
 * @since 2019-09-02
 */

namespace JMichaelWard\BoardGameCollector\Admin\Settings;

/**
 * Interface SettingsFields
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2019-09-02
 * @package JMichaelWard\BoardGameCollector\Admin\Settings
 */
interface SettingsFields {
	/**
	 * The main settings key.
	 */
	public const SETTINGS_KEY = 'bgc-settings';

	/**
	 * The username field.
	 */
	public const USERNAME_KEY = 'bgg-username';

	/**
	 * The update with images field.
	 */
	public const UPDATE_WITH_IMAGES_KEY = 'bgg-update-with-images';
}
