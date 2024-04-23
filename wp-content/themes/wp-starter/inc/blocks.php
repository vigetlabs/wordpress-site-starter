<?php
/**
 * Block Functions
 *
 * @package WPStarter
 */

// Add Components block Category.
add_filter(
	'block_categories_all',
	function ( array $categories ): array {
		array_unshift(
			$categories,
			[
				'slug'  => 'components',
				'title' => __( 'Components', 'wp-starter' ),
			]
		);
		return $categories;
	}
);

add_action(
	'init',
	function () {
		register_block_style(
			'core/paragraph',
			[
				'name'       => 'default',
				'label'      => __( 'Default', 'wp-starter' ),
				'is_default' => true,
			]
		);

		register_block_style(
			'core/paragraph',
			[
				'name'  => 'eyebrow',
				'label' => __( 'Eyebrow', 'wp-starter' ),
			]
		);
	}
);
