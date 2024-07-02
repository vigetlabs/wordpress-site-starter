<?php
/**
 * Plugin assets
 *
 * @package ACFFormBlocks
 */

add_action(
	'admin_enqueue_scripts',
	function () {
		wp_enqueue_style(
			'acfformblocks-admin',
			ACFFB_PLUGIN_URL . 'assets/css/admin.css',
			[],
			ACFFB_VERSION
		);
	}
);
