<?php
/**
 * HTML markup for the Board Game Collector API plugin settings page.
 *
 * @package JMichaelWard\BoardGameCollector
 */

?>

<form method="POST" action="options.php">
	<?php settings_fields( 'bgg-settings' ); ?>
	<?php do_settings_sections( 'bgg-settings' ); ?>
	<?php submit_button(); ?>
</form>
