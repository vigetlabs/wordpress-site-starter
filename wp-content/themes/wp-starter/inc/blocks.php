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
