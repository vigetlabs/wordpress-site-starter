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
?>
<InnerBlocks
	<?php block_attrs( $block ); ?>
	template="<?php echo esc_attr( wp_json_encode( $block_template ) ); ?>"
/>
