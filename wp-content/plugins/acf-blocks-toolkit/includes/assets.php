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

add_action(
	'enqueue_block_assets',
	function () {
		$asset_file   = include ACFBT_PLUGIN_PATH . 'build/index.asset.php';
		$dependencies = array_merge( $asset_file['dependencies'], [ 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' ] );

		wp_register_script(
			'acfbt-editor-scripts',
			ACFBT_PLUGIN_URL . 'build/index.js',
			$dependencies,
			$asset_file['version']
		);

		wp_register_style(
			'acfbt-editor-styles',
			ACFBT_PLUGIN_URL . 'build/editor.css',
			[],
			ACFBT_VERSION
		);

		wp_set_script_translations(
			'acfbt-editor-scripts',
			'acf-blocks-toolkit',
			ACFBT_PLUGIN_URL . 'languages'
		);
	}
);

add_action(
	'enqueue_block_assets',
	function () {
		wp_enqueue_script( 'acfbt-editor-scripts' );
		wp_enqueue_style( 'acfbt-editor-styles' );
	},
	30
);
