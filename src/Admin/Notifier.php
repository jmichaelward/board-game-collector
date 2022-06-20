<?php
/**
 * Class to handle admin notifications.
 *
 * @package JMichaelWard\BoardGameCollector\Admin
 */

namespace JMichaelWard\BoardGameCollector\Admin;

/**
 * Class Notifier
 *
 * @package JMichaelWard\BoardGameCollector\Admin
 */
class Notifier {
	/**
	 * Create an error notice.
	 *
	 * @param string $message The text of the notice to display.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-07-20
	 * @return void
	 */
	public function do_error_notice( string $message ): void {
		add_action( 'bgc_error_notify', [ $this, 'display_error_notice' ], 10, 1 );

		add_action(
			'admin_notices',
			function() use ( $message ) {
				do_action( 'bgc_error_notify', $message );
			}
		);
	}

	/**
	 * Display an error notice in the admin.
	 *
	 * @param string $message The error message to display.
	 *
	 * @author Jeremy Ward <jeremy@jmichaelward.com>
	 * @since  2019-07-20
	 * @return void
	 */
	public function display_error_notice( string $message ): void { ?>
		<div class="notice notice-error is-dismissible">
			<p><?php echo esc_html( $message ); ?></p>
		</div>
		<?php
	}

	/**
	 * Notify admin that they have not yet configured the plugin settings.
	 */
	public function do_warning_settings_not_configured(): void {
		$settings_url = add_query_arg(
			[
				'post_type' => 'bgc_game',
				'page' => 'bgc-settings',
			],
			admin_url( 'edit.php' )
		);

		$message = sprintf(
			'Your games collection will not update until a valid username is entered into the <a href="%s">Board Game Collector settings page</a>.',
			esc_url( $settings_url ),
		);

		?>
		<div class="notice notice-warning is-dismissible">
			<p><?php echo wp_kses_post( $message ); ?></p>
		</div>
		<?php
	}
}
