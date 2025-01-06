<?php
/**
 * Block: Form Meta
 *
 * @global array $block
 * @global int   $post_id
 *
 * @package VigetFormBlocks
 */

use VigetFormBlocks\Form;
use Viget\VigetBlocksToolkit\BlockTemplate\Block;
use Viget\VigetBlocksToolkit\BlockTemplate\Template;

$meta_field = get_field( 'meta_field' );
$form_id    = get_field( '_vgtfb_form_id', $post_id );

if ( ! $form_id || ! $meta_field ) {
	printf(
		'<p style="text-align: center">%s</p>',
		esc_html__( 'Select a form and meta field to see preview.', 'viget-form-blocks' )
	);
	return;
}

$form = Form::find_form( $form_id );
$meta = $form?->get_meta_field( $meta_field );

$show_label = get_field( 'show_label' );
$show_value = get_field( 'show_value' );

$template = ( new Template(
	new Block(
		'core/paragraph',
		[ 'placeholder' => $meta->get_label() ?? __( 'Field Label...', 'viget-form-blocks' ) ]
	),
) )->get();
?>
<section <?php block_attrs( $block ); ?>>
	<label clas="acffb-form-meta">
		<?php if ( $show_label ) : ?>
			<?php inner_blocks( [ 'template' => $template ] ); ?>
		<?php endif; ?>

		<?php if ( $show_value ): ?>
			<span class="acffb-value-placeholder">
				<?php
				esc_html_e( 'Value', 'viget-form-blocks' );

				if ( ! $show_label ) :
					esc_html_e( ' for ', 'viget-form-blocks' );
					echo esc_html( $meta->get_label() );
				endif;
				?>
			</span>
		<?php endif; ?>
	</label>
</section>
