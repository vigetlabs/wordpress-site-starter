<?php
/**
 * Plugin assets
 *
 * @package VigetFormBlocks
 */

add_action(
	'wp_enqueue_scripts',
	function () {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script(
			'mf-conditional-fields',
			VGTFB_PLUGIN_URL . 'assets/js/third-party/mf-conditional-fields' . $min . '.js',
			[],
			'1.0.6',
			[
				'in_footer' => true,
			]
		);

		wp_register_script(
			'VigetFormBlocks-conditional-logic',
			VGTFB_PLUGIN_URL . 'assets/js/conditional-logic.js',
			[ 'mf-conditional-fields' ],
			VGTFB_VERSION,
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
			'VigetFormBlocks-admin',
			VGTFB_PLUGIN_URL . 'assets/css/admin.css',
			[],
			VGTFB_VERSION
		);

		wp_enqueue_script(
			'VigetFormBlocks-admin',
			VGTFB_PLUGIN_URL . 'assets/js/admin.js',
			[],
			VGTFB_VERSION
		);

		wp_localize_script(
			'VigetFormBlocks-admin',
			'acffbAdmin',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'acffb' ),
			]
		);
	}
);
