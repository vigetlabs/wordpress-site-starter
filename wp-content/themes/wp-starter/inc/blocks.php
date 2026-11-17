<?php
/**
 * Block Functions
 *
 * @package WPStarter
 */

namespace WPStarter;

// Default to block version 3.
add_filter(
	'acf/blocks/default_block_version',
	function( $version, $block ) {
		if ( ! empty( $block['blockVersion'] ) ) {
			return $block['blockVersion'];
		}

		return 3;
	},
	10,
	2
);

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
	function ( array|bool $allowed_block_types, \WP_Block_Editor_Context $context ): array|bool {
		// TODO: Maybe filter by $context.

		if ( ! is_array( $allowed_block_types ) ) {
			$allowed_block_types = array_keys( \WP_Block_Type_Registry::get_instance()->get_all_registered() );
		}

		$removed_blocks = [
			'core/archives',
			'core/audio',
			'core/calendar',
			'core/html',
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

// Register Button Block Styles.
add_action(
	'init',
	function () {
		register_block_style(
			'core/paragraph',
			[
				'name'  => 'eyebrow',
				'label' => __( 'Eyebrow', 'wp-starter' ),
			]
		);

		register_block_style(
			'core/button',
			[
				'name'  => 'icon-only',
				'label' => __( 'Icon Only', 'wp-starter' ),
			]
		);

		register_block_style(
			'core/button',
			[
				'name'  => 'plain',
				'label' => __( 'Plain', 'wp-starter' ),
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

		$logo_file = Vite::get_instance()->get_image_path( 'logo.svg' );

		if ( ! file_exists( $logo_file ) ) {
			return $html;
		}

		$aria_current = is_front_page() && ! is_paged() ? ' aria-current="page"' : '';
		$sample_logo  = sprintf(
			'<img src="%1$s" />',
			Vite::get_instance()->get_image_url( 'logo.svg' )
		);

		return sprintf(
			'<a href="%s" class="custom-logo-link" rel="home"%s>%s</a>',
			esc_url( home_url( '/' ) ),
			$aria_current,
			$sample_logo
		);
	}
);

// Wrap an Icon Only button's text for screen readers only, before Viget Blocks Toolkit adds the icon at 10.
add_filter(
	'render_block_core/button',
	function ( string $block_content, array $block ): string {
		if (
			! in_array( 'is-style-icon-only', explode( ' ', $block['attrs']['className'] ?? '' ), true )
			|| str_contains( $block_content, 'wp-block-button__link-text' )
		) {
			return $block_content;
		}

		return (string) preg_replace_callback(
			'/(<(a|button)\b[^>]*>)(.*?)(<\/\2>)/s',
			function ( array $link ): string {
				// Leave out an icon already in the link.
				$icon = '<span\b[^>]*\bwp-block-button__link-icon\b[^>]*>.*?<\/span>';

				if ( ! preg_match( '/^(\s*' . $icon . ')?(.*?)(' . $icon . '\s*)?$/s', $link[3], $parts ) ) {
					return $link[0];
				}

				if ( '' === trim( wp_strip_all_tags( $parts[2] ) ) ) {
					return $link[0];
				}

				return $link[1] . ( $parts[1] ?? '' ) . '<span class="wp-block-button__link-text">' . $parts[2] . '</span>' . ( $parts[3] ?? '' ) . $link[4];
			},
			$block_content,
			1
		);
	},
	9,
	2
);
