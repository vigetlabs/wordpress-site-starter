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

$key   = $block->get_block_data( 'form_field' );
$field = $form->get_form_object()->get_field_by_id( $key );
$label = $field->get_field_label() ?: $content[ $key ]['label'];

?>
<div class="acffb-form-data-block">
	<?php
	printf(
		'<label>%s</label>',
		esc_html( $label )
	);
	$field->render_value( $content[ $key ]['value'], $form );
	?>
</div>
