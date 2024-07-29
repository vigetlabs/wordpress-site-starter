<?php
/**
 * Form Data Default Template
 *
 * @global FormDataBlock $block
 * @global ?Form         $form
 * @global array         $content
 */

use ACFFormBlocks\Blocks\FormDataBlock;
use ACFFormBlocks\Form;

if ( ! isset( $form ) && isset( $block ) ) {
	$form = $block->get_form();
}

$field_id   = $block->get_block_data( 'form_field' );
$show_label = $block->get_block_data( 'show_label' );
$show_value = $block->get_block_data( 'show_value' );

$field = $form->get_form_object()->get_field_by_id( $field_id );
$label = $field->get_field_label() ?: $content[ $field_id ]['label'];

?>
<div class="acffb-form-data-block">
	<?php
	if ( $show_label ) :
		printf(
			'<label>%s</label>',
			esc_html( $label )
		);
	endif;

	if ( $show_value ) :
		$field->render_value( $content[ $field_id ]['value'], $form );
	endif;
	?>
</div>
