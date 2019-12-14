<?php
/**
 * HTML markup for the Board Game Collector API plugin settings page.
 *
 * @package JMichaelWard\BoardGameCollector
 */

?>

<form method="POST" action="options.php">
	<?php settings_fields( 'bgc-settings' ); ?>
	<?php do_settings_sections( 'bgc-settings' ); ?>
	<?php submit_button(); ?>
	<div id="bgc-app"></div>
</form>
