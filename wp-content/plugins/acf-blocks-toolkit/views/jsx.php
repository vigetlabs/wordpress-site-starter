<?php
/**
 * Default JSX Template
 *
 * @global array $block
 *
 * @package ACFBlocksToolkit
 */

if ( ! isset( $block_template ) && empty( $block['template'] ) ) {
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
	'template' => $block['template'] ?? $block_template ?? [],
];
?>
<section <?php block_attrs( $block ); ?>>
	<div class="acf-block-inner__container">
		<?php inner_blocks( $inner ); ?>
	</div>
</section>
