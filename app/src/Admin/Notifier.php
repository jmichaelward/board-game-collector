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
}
