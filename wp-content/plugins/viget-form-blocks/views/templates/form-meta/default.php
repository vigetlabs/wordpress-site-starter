<?php
/**
 * Form Meta Default Template
 *
 * @global FormDataBlock $block
 * @global ?Form         $form
 * @global array         $meta
 */

use VigetFormBlocks\Blocks\FormDataBlock;
use VigetFormBlocks\Form;

if ( ! isset( $form ) && isset( $block ) ) {
	$form = $block->get_form();
}

$meta_key   = $block->get_block_data( 'meta_field' );
$show_label = $block->get_block_data( 'show_label' );
$show_value = $block->get_block_data( 'show_value' );
$meta_field = $form->get_meta_field( $meta_key );

if ( ! empty( $meta[ $meta_key ]['value'] ) ) {
	$meta_field->set_value( $meta[ $meta_key ]['value'] );
}

$value = $meta_field->get_value( null, true );
?>
<div class="acffb-form-meta-block">
	<?php
	if ( is_array( $value ) ) {
		foreach ( $value as $key => $val ) {
			if ( $show_label ) :
				printf(
					'<label>%s:</label>',
				$meta_field->get_label( $key )
				);
			endif;

			if ( $show_value && $val ) :
				echo wp_kses_post( $val );
			endif;
		}
	} else {
		if ( $show_label ) :
			printf(
				'<label>%s:</label>',
				$meta_field->get_label()
			);
		endif;

		if ( $show_value && $value ) :
			echo wp_kses_post( $value );
		endif;
	}
	?>
</div>
