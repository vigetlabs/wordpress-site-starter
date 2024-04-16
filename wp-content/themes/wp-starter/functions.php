<?php
/**
 * WP Starter functions
 *
 * @package WPStarter
 */

add_action(
	'after_setup_theme',
	function () {
		remove_theme_support( 'core-block-patterns' );
	}
);

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
		register_block_pattern_category(
			'cta',
			[
				'label' => __( 'Call To Action', 'wp-starter' ),
			]
		);
	}
);
