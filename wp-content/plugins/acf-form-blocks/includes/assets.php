<?php
/**
 * Plugin assets
 *
 * @package ACFFormBlocks
 */

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_register_script(
			'mf-conditional-fields',
			ACFFB_PLUGIN_URL . 'assets/js/third-party/mf-conditional-fields.min.js',
			[],
			ACFFB_VERSION,
			[
				'in_footer' => true,
			]
		);

		wp_register_script(
			'acfformblocks-conditional-logic',
			ACFFB_PLUGIN_URL . 'assets/js/conditional-logic.js',
			[ 'mf-conditional-fields' ],
			ACFFB_VERSION,
			[
				'in_footer' => true,
			]
		);
	}
);

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
