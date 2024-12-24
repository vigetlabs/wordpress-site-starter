<?php
/**
 * Block: Input
 *
 * @global array $block
 * @global array $context
 * @global WP_Block $wp_block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Elements\Input;

/** @var Input $field */
$field = Field::factory( $block, $context, $wp_block );
$inner = [
	'template' => $field->get_template()
];
?>
<div class="form-input type-input input-type-<?php echo esc_attr( $field->get_input_type() ); ?>">
	<?php inner_blocks( $inner ); ?>

	<input <?php block_attrs( $block, '', $field->get_attrs() ); ?> />

	<?php
	if ( is_admin() && 'hidden' === $field->get_input_type() ) {
		printf( '<p><em>%s</em></p>', __( 'This field is hidden.', 'acf-form-blocks' ) );
	}
	?>
</div>
