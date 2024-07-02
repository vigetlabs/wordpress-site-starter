<?php
/**
 * Block: Textarea
 *
 * @global array $block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Block;
use ACFFormBlocks\Field;
use ACFFormBlocks\Template;

$field = new Field( $block );

$inner = [
	'template' => ( new Template( new Block( 'core/paragraph', [ 'placeholder' => __( 'Field Label...', 'acf-form-blocks' ) ] ) ) )->get(),
];
?>
<div class="form-input type-textarea">
	<label<?php
		if ( ! is_admin() ) :
			printf( ' for="%s"', esc_attr( get_block_id( $block ) ) );
		endif;
	?>>
		<?php inner_blocks( $inner ); ?>
		<?php if ( $field->is_required() ) : ?>
			<span class="is-required">*</span>
		<?php endif; ?>
	</label>
	<textarea
		name="<?php echo esc_attr( $field->get_name() ); ?>"
		<?php block_attrs( $block ); ?>
	><?php echo esc_textarea( $field->get_value() ); ?></textarea>
</div>
