<?php
/**
 * Add ACF Blocks to Parts Kit
 *
 * @package ACFBlocksToolkit
 */

use Viget\ACFBlocksToolkit\Block_Registration;
use VigetPartsKit\PartsKit;

add_filter(
	'viget_parts_kit',
	function( array $parts ): array {
		$blocks = Block_Registration::get_all_blocks();

		foreach ( $blocks as $block ) {
			$block_name = str_contains( $block['name'], '/' ) ? $block['name'] : 'acf/' . $block['name'];

			if ( isset( $parts[ $block_name ] ) ) {
				continue;
			}

			$parts[ $block_name ] = [
				'title'    => $block['title'],
				'url'      => home_url( PartsKit::URL_SLUG . '/' . urlencode( $block_name ) ),
				'children' => [],
			];
		}

		return $parts;
	}
);

add_filter(
	'viget_parts_kit_%',
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

		if ( empty( $block['data'] ) ) {
			$block['data'] = [];
		}

		$block['data'] = array_merge( $block['data'], acfbt_get_sample_props( $block ) );

		ob_start();
		acf_render_block( $block, '', true );
		return ob_get_clean();
	},
	10,
	2
);

/**
 * Get Sample Block properties
 *
 * @param array $block
 *
 * @return array
 */
function acfbt_get_sample_props( array $block ): array {
	$props  = [];
	$fields = get_fields( $block['name'] );

	if ( ! is_array( $fields ) ) {
		return $props;
	}

	foreach ( $fields as $field ) {
		$props[ $field['name'] ] = acfbt_get_sample_data( $field );
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
function acfbt_get_sample_data( array $field ): string|array {
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
