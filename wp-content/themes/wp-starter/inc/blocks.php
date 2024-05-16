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

add_filter(
	'allowed_block_types_all',
	function ( array|bool $allowed_block_types, WP_Block_Editor_Context $context ): array|bool {
		if ( ! is_array( $allowed_block_types ) ) {
			$allowed_block_types = array_keys( WP_Block_Type_Registry::get_instance()->get_all_registered() );
		}

		$removed_blocks = [
			'core/classic',
			'core/details',
			'core/preformatted',
			'core/verse',
			'core/gallery',
			'core/audio',
			'core/cover',
			'core/media-text',
			'core/video',
			'core/more',
			'core/nextpage',
			'core/archives',
			'core/calendar',
			'core/rss',
			'core/html',
			'core/latest-comments',
			'core/latest-posts',
		];

		return array_diff( $allowed_block_types, $removed_blocks );
	},
	10,
	2
);
