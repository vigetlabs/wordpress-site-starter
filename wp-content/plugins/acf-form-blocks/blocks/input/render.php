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
	<label<?php
		if ( ! is_admin() ) :
			printf( ' for="%s"', esc_attr( $field->get_id_attr() ) );
		endif;
	?>>
		<?php inner_blocks( $inner ); ?>
		<?php if ( $field->is_required() ) : ?>
			<span class="is-required">*</span>
		<?php endif; ?>
	</label>

	<input <?php acffb_block_attrs( $field ); ?> />
</div>
