<?php
/**
 * Add ACF Blocks to Parts Kit
 *
 * @package VigetBlocksToolkit
 */

use Viget\VigetBlocksToolkit\Block_Registration;

add_filter(
	'viget_parts_kit_block_%',
	function ( string $output, string $block_name ): string {
		$block = Block_Registration::get_block( $block_name );

		if ( ! $block ) {
			return $output;
		}

		if ( ! empty( $block['path'] ) ) {
			$include = $block['path'] . '/block.php';

			// Autoload block.php within block directory
			if ( file_exists( $include ) ) {
				require_once $include;
			}
		}

		return vgtbt_parse_inner_blocks( $output );
	},
	10,
	2
);

/**
 * Parse InnerBlocks Template
 *
 * @param string $output
 *
 * @return string
 */
function vgtbt_parse_inner_blocks( string $output ): string {
	// Check if InnerBlocks tag has a template attribute with regular expression
	if ( ! preg_match( '/<InnerBlocks[^>]*template="([^"]*)"[^>]*>/i', $output, $matches ) ) {
		return $output;
	}

	// Get the template attribute value
	$template = $matches[1];
	$template = htmlspecialchars_decode( $template );
	$template = json_decode( $template, true );

	$content = '';

	foreach ( $template as $block_array ) {
		$block = [
			'blockName'   => $block_array[0],
			'attrs'       => $block_array[1] ?? [],
			'innerBlocks' => $block_array[2] ?? [],
		];

		$block    = vgtbt_add_sample_data( $block );
		$content .= apply_filters( 'the_content', trim( render_block( $block ) ) );
		$content  = vgtbt_parse_inner_blocks( $content );
		$content  = vgtbt_fill_empty_tags( $content );
	}

	$content = str_replace( '$', '\$', $content );

	// Replace the InnerBlocks tag with the parsed content
	return preg_replace( '/<InnerBlocks[^>]*\/>/i', $content, $output );
}

/**
 * Get Sample Block properties
 *
 * @param array $block
 *
 * @return array
 */
function vgtbt_get_sample_props( array $block ): array {
	$props  = [];
	$fields = get_fields( $block['name'] );

	if ( ! is_array( $fields ) ) {
		return $props;
	}

	foreach ( $fields as $field ) {
		$props[ $field['name'] ] = vgtbt_get_sample_data( $field );
	}

	return $props;
}

/**
 * Return Sample Parts Kit Data
 *
 * @param array $field
 *
 * @return string|array
 */
function vgtbt_get_sample_data( array $field ): string|array {
	if ( in_array( $field['type'], [ 'text', 'select' ], true ) ) {
		return 'Lorem Ipsum';
	}

	if ( 'image' === $field['type'] ) {
		return [
			'ID'          => 000,
			'id'          => 000,
			'title'       => 'placeholder',
			'filename'    => '600x400.svg',
			'filesize'    => 3270,
			'url'         => 'https://placehold.co/600x400/EEE/31343C',
			'link'        => 'https://placehold.co/600x400/EEE/31343C',
			'alt'         => '600x400 Placeholder',
			'author'      => 1,
			'description' => 'A 600x400 placeholder image.',
			'caption'     => 'This is a placeholder',
			'name'        => '600x400',
			'status'      => 'inherit',
			'uploaded_to' => 0,
			'date'        => '2023-09-24 13:12:00',
			'modified'    => '2023-09-24 13:12:00',
			'menu_order'  => 0,
			'mime_type'   => 'image/svg+xml',
			'type'        => 'image',
			'subtype'     => 'svg',
			'icon'        => 'https://viget-wp-boilerplate.vgt.site/wp-includes/images/media/default.png',
			'width'       => 600,
			'height'      => 400,
			'sizes'       => [
				'thumbnail'           => 'https://placehold.co/150x150/EEE/31343C',
				'thumbnail-width'     => 150,
				'thumbnail-height'    => 150,
				'medium'              => 'https://placehold.co/226x300/EEE/31343C',
				'medium-width'        => 226,
				'medium-height'       => 300,
				'large'               => 'https://placehold.co/771x1024/EEE/31343C',
				'large-width'         => 771,
				'large-height'        => 1024,
			],
		];
	}

	if ( 'wysiwyg' === $field['type'] ) {
		return '<p>Lorem Ipsum</p>';
	}

	return 'Unsupported.';
}

/**
 * Add Sample Data to Block
 *
 * @param array $block
 *
 * @return array
 */
function vgtbt_add_sample_data( array $block ): array {
	$supported = [
		'core/image'     => [
			'url'    => vgtbt_get_sample_data( [ 'type' => 'image' ] )[ 'url' ],
			'width'  => vgtbt_get_sample_data( [ 'type' => 'image' ] )[ 'width' ],
			'height' => vgtbt_get_sample_data( [ 'type' => 'image' ] )[ 'height' ],
		],
		'core/heading'   => [
			'content' => vgtbt_get_sample_data( [ 'type' => 'text' ] ),
		],
		'core/paragraph' => [
			'content' => vgtbt_get_sample_data( [ 'type' => 'text' ] ),
		],
		'core/button'    => [
			'text' => vgtbt_get_sample_data( [ 'type' => 'text' ] ),
			'url'  => '#',
		],
		'core/details'   => [
			'summary' => vgtbt_get_sample_data( [ 'type' => 'text' ] ),
		],
	];

	if ( ! array_key_exists( $block['blockName'], $supported ) ) {
		return $block;
	}

	foreach ( $supported[ $block['blockName'] ] as $attr => $value ) {
		if ( empty( $block['attrs'][ $attr ] ) ) {
			$block['attrs'][ $attr ] = $value;
		}
	}

	return $block;
}

/**
 * Add sample content to empty paragraph, heading, summary, etc.
 *
 * @param string $content
 *
 * @return string
 */
function vgtbt_fill_empty_tags( string $content ): string {
	$sample_text = vgtbt_get_sample_data( [ 'type' => 'text' ] );

	$patterns = [
		'/<(p[^>]*)><\/(p)>/i',
		'/<(h[1-6][^>]*)><\/(h[1-6])>/i',
	];

	foreach ( $patterns as $pattern ) {
		$content = preg_replace( $pattern, '<$1>' . $sample_text . '</$2>', $content );
	}

	return $content;
}
