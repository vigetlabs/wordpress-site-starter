<?php
/**
 * Custom Scripts Admin Page
 *
 * @package VigetWP
 */

use VigetWP\Admin\CustomScripts;

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Custom Scripts', 'viget-wp' ); ?></h1>

	<?php settings_errors(); ?>

	<form method="post" action="<?php echo admin_url( 'tools.php?page=' . CustomScripts::PAGE_SLUG ); ?>">
		<?php
		wp_nonce_field( 'vigetwp-custom-scripts', '_' . CustomScripts::OPTION_NAME . '_nonce' );
		settings_fields( CustomScripts::OPTION_NAME );
		do_settings_sections( CustomScripts::OPTION_NAME );
		submit_button();
		?>
	</form>
</div>
