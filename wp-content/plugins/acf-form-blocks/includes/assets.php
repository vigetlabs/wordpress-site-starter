<?php
/**
 * Plugin assets
 *
 * @package ACFFormBlocks
 */

add_action(
	'wp_enqueue_scripts',
	function () {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script(
			'mf-conditional-fields',
			ACFFB_PLUGIN_URL . 'assets/js/third-party/mf-conditional-fields' . $min . '.js',
			[],
			'1.0.6',
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
