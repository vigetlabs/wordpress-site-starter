<?php
/**
 * Block registration
 *
 * @package ACFFormBlocks
 */

add_filter(
	'acfbt_block_locations',
	function ( array $locations ): array {
		$locations[] = ACFFB_PLUGIN_PATH . 'blocks';
		return $locations;
	}
);

add_filter(
	'block_categories_all',
	function ( array $categories ): array {
		$categories[] = [
			'slug'  => 'forms',
			'title' => __( 'Forms', 'acf-form-blocks' ),
		];
		$categories[] = [
			'slug'  => 'form-data',
			'title' => __( 'Form Data', 'acf-form-blocks' ),
		];
		return $categories;
	}
);

add_action(
	'acf/init',
	function() {
		acf_add_options_page(
			[
				'page_title'      => __( 'Form Blocks Admin', 'acf-form-blocks' ),
				'menu_slug'       => 'acffb-forms',
				'menu_title'      => __( 'Form Blocks', 'acf-form-blocks' ),
				'position'        => 20,
				'redirect'        => false,
				'description'     => __( 'Admin for Form Blocks', 'acf-form-blocks' ),
				'menu_icon'       => [
					'type'  => 'dashicons',
					'value' => 'dashicons-feedback',
				],
				'updated_message' => __( 'Settings Updated', 'acf-form-blocks' ),
				'capability'      => 'manage_options',
				'autoload'        => true,
				'icon_url'       => 'dashicons-feedback',
			]
		);
	}
);

