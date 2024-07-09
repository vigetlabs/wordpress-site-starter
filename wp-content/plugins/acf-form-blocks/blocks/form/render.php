<?php
/**
 * Block: Form
 *
 * @global array $block
 *
 * @package ACFFormBlocks
 */

$form = acffb_get_form( $block );

wp_enqueue_script( 'acfformblocks-conditional-logic' );

$inner = [
	'template' => $form->get_form_object()->get_template(),
];
?>
<?php if ( $form->get_submission()->is_processed() ) : ?>
	<div <?php block_attrs( $block, 'form-placeholder' ); ?>>
		<?php $form->get_confirmation()->render(); ?>
	</div>
<?php else : ?>
	<form <?php block_attrs( $block ); ?>>
		<?php $form->get_validation()->render(); ?>

		<?php inner_blocks( $inner ); ?>
	</form>
<?php endif; ?>

