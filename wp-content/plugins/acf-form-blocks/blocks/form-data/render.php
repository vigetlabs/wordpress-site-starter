<?php
/**
 * Block: Form Data
 *
 * @global array $block
 * @global int   $post_id
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Form;
use ACFFormBlocks\Utilities\BlockTemplate\Block;
use ACFFormBlocks\Utilities\BlockTemplate\Template;

$form_field = get_field( 'form_field' );
$form_id    = get_field( '_acffb_form_id', $post_id );

if ( ! $form_id || ! $form_field ) {
	printf(
		'<p style="text-align: center">%s</p>',
		esc_html__( 'Select a form and field to see preview.', 'acf-form-blocks' )
	);
	return;
}

$form  = Form::get_instance( $form_id );
$field = $form->get_form_object()->get_field_by_id( $form_field );

$label = get_field( 'show_label' );
$value = get_field( 'show_value' );

$template = ( new Template(
	new Block(
		'core/paragraph',
		[ 'placeholder' => $field->get_label() ]
	),
) )->get();
?>
<section <?php block_attrs( $block ); ?>>
	<label clas="acffb-form-data">
		<?php if ( $label ) : ?>
			<?php inner_blocks( [ 'template' => $template ] ); ?>
		<?php endif; ?>
		<span class="acffb-value-placeholder">
			<?php esc_html_e( 'Value', 'acf-form-blocks' ); ?>
			<?php if ( ! $label ) : ?>
				<?php esc_html_e( 'for', 'acf-form-blocks' ); ?>
				<?php echo esc_html( $field->get_label() ); ?>
			<?php endif; ?>
		</span>
	</label>
</section>
