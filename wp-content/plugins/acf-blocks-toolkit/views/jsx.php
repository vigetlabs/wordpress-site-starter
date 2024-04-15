<?php
/**
 * Default JSX Template
 *
 * @global array $block
 *
 * @package ACFBlocksToolkit
 */

if ( ! isset( $block_template ) ) {
	$block_template = [
		[
			'core/paragraph',
			[
				'placeholder' => __( 'Type / to choose a block', 'acf-blocks-toolkit' ),
			],
		],
	];
}

$inner = [
	'template' => $block_template,
];

inner_blocks( $inner );
