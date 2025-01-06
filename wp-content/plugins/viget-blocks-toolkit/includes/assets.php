<?php
/**
 * Assets
 *
 * @package VigetBlocksToolkit
 */

add_action(
	'admin_enqueue_scripts',
	function () {
		wp_enqueue_style(
			'vgtbt-admin-css',
			VGTBT_PLUGIN_URL . 'assets/css/admin.css',
			[],
			VGTBT_VERSION
		);
	}
);

add_action(
	'enqueue_block_editor_assets',
	function () {
		$asset_file   = include VGTBT_PLUGIN_PATH . 'build/index.asset.php';
		$dependencies = array_merge( $asset_file['dependencies'], [ 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' ] );

		wp_register_script(
			'vgtbt-editor-scripts',
			VGTBT_PLUGIN_URL . 'build/index.js',
			$dependencies,
			$asset_file['version']
		);

		wp_register_style(
			'vgtbt-editor-styles',
			VGTBT_PLUGIN_URL . 'build/editor.css',
			[],
			VGTBT_VERSION
		);

		wp_set_script_translations(
			'vgtbt-editor-scripts',
			'viget-blocks-toolkit',
			VGTBT_PLUGIN_URL . 'languages'
		);
	}
);

add_action(
	'enqueue_block_editor_assets',
	function () {
		wp_enqueue_script( 'vgtbt-editor-scripts' );
		wp_enqueue_style( 'vgtbt-editor-styles' );
	},
	30
);

add_action(
	'enqueue_block_assets',
	function () {
		$asset_file = include VGTBT_PLUGIN_PATH . 'build/style.asset.php';

		wp_enqueue_style(
			'vgtbt-block-styles',
			VGTBT_PLUGIN_URL . 'build/style.css',
			[],
			$asset_file['version']
		);
	}
);
