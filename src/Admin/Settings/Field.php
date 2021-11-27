<?php
/**
 * Individual field for the settings page.
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2020-09-13
 * @package JMichaelWard\BoardGameCollector\Admin\Settings
 */

namespace JMichaelWard\BoardGameCollector\Admin\Settings;

/**
 * Class Field
 *
 * @author  Jeremy Ward <jeremy@jmichaelward.com>
 * @since   2020-09-13
 * @package JMichaelWard\BoardGameCollector\Admin\Settings
 */
abstract class Field implements FieldInterface {
	/**
	 * Object which implements a settings interface.
	 *
	 * @var SettingsInterface
	 */
	protected $settings;

	/**
	 * The field key.
	 *
	 * @var string
	 */
	protected $key;

	/**
	 * The field label.
	 *
	 * @var string
	 */
	protected $label;

	/**
	 * The type of field.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Field constructor.
	 *
	 * @param SettingsInterface $settings Object implementing the SettingsInterface.
	 * @param string            $key      The key used to identify the field.
	 * @param string            $label    The label for the field.
	 * @param string            $type     The type of field.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2020-09-13
	 */
	public function __construct( SettingsInterface $settings, string $key, string $label, string $type ) {
		$this->settings = $settings;
		$this->key      = $key;
		$this->label    = $label;
		$this->type     = $type;
	}

	/**
	 * Get the key.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2020-09-13
	 * @return string
	 */
	public function get_key(): string {
		return $this->key;
	}

	/**
	 * Get the label.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2020-09-13
	 * @return string
	 */
	public function get_label(): string {
		return $this->label;
	}

	/**
	 * Get the type.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2020-09-13
	 * @return string
	 */
	public function get_type(): string {
		return $this->type;
	}
}
