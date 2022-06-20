<?php
/**
 * HTML markup for the Board Game Collector API plugin settings page.
 *
 * @package JMichaelWard\BoardGameCollector
 */

?>

<div id="bgc-settings">
	<form method="POST" action="options.php">
		<?php settings_fields( 'bgc-settings' ); ?>
		<?php do_settings_sections( 'bgc-settings' ); ?>

		<?php submit_button(); ?>
	</form>

	<div id="bgc-app"></div>
</div>

