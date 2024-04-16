<?php
/**
 * Assets
 *
 * @package ACFBlocksToolkit
 */

add_action(
	'admin_enqueue_scripts',
	function () {
		wp_enqueue_style(
			'acfbt-admin-css',
			ACFBT_PLUGIN_URL . 'assets/css/admin.css',
			[],
			ACFBT_VERSION
		);
	}
);
