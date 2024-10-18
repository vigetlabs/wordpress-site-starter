<?php
/**
 * Block: Form
 *
 * @global array $block
 * @global array $context
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Form;

$form = Form::get_instance( $block, '', $context );

wp_enqueue_script( 'acfformblocks-conditional-logic' );

$inner = [
	'template' => $form->get_form_object()->get_template(),
];

$attrs = $form->get_form_object()->get_attrs();
?>
<?php if ( $form->get_submission()->is_processed() ) : ?>
	<div <?php block_attrs( $block, 'form-placeholder', $attrs ); ?>>
		<?php $form->get_confirmation()->render(); ?>
	</div>
<?php else : ?>
	<form <?php block_attrs( $block, '', $attrs ); ?>>
		<?php $form->get_validation()->render(); ?>

		<?php inner_blocks( $inner ); ?>
	</form>
<?php endif; ?>

