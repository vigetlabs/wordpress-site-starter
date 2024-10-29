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

// Disable some of the default core blocks.
add_filter(
	'allowed_block_types_all',
	function ( array|bool $allowed_block_types, WP_Block_Editor_Context $context ): array|bool {
		// TODO: Maybe filter by $context.

		if ( ! is_array( $allowed_block_types ) ) {
			$allowed_block_types = array_keys( WP_Block_Type_Registry::get_instance()->get_all_registered() );
		}

		$removed_blocks = [
			'core/archives',
			'core/audio',
			'core/calendar',
			'core/html',
			'core/latest-comments',
			'core/latest-posts',
			'core/more',
			'core/nextpage',
			'core/preformatted',
			'core/rss',
			'core/verse',
		];

		return array_values( array_diff( $allowed_block_types, $removed_blocks ) );
	},
	10,
	2
);

// Register Icon Only Block Style
add_action(
	'init',
	function () {
		register_block_style(
			'core/button',
			[
				'name'  => 'icon-only',
				'label' => __( 'Icon Only', 'wp-starter' ),
			]
		);
	}
);

// Set the default logo.
add_filter(
	'get_custom_logo',
	function ( string $html ): string {

		if ( has_custom_logo() ) {
			return $html;
		}

		$logo_path = '/src/images/logo.svg';
		$logo_file = get_stylesheet_directory() . $logo_path;

		if ( ! file_exists( $logo_file ) ) {
			return $html;
		}

		$aria_current = is_front_page() && ! is_paged() ? ' aria-current="page"' : '';
		$sample_logo  = sprintf(
			'<img src="%1$s" />',
			get_theme_file_uri( $logo_path )
		);

		return sprintf(
			'<a href="%s" class="custom-logo-link" rel="home"%s>%s</a>',
			esc_url( home_url( '/' ) ),
			$aria_current,
			$sample_logo
		);
	}
);
