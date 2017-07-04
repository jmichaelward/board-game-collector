<?php
namespace JMichaelWard\BoardGameCollector\Admin;

/**
 * Class Notifier
 *
 * @package JMichaelWard\BoardGameCollector\Admin
 */
class Notifier {
	/**
	 * Notify admin that the composer install command has not been run for this plugin.
	 */
	public function do_error_message_missing_autoloader() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'BoardGameCollector Data classes not found. Did you run composer install?', 'bgc' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Notify admin that they have not yet configured the plugin settings.
	 */
	public function do_warning_settings_not_configured() {
		$message = 'A BoardGameGeek username has not yet been saved on the BGG Settings page. Your games collection '
					. 'will not download until a valid username is entered.';
		?>
		<div class="notice notice-warning is-dismissible">
			<p><?php echo esc_html( $message ); ?></p>
		</div>
		<?php
	}
}
