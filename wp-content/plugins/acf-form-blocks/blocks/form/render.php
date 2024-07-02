<?php
/**
 * Block: Form
 *
 * @global array $block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Block;
use ACFFormBlocks\Form;
use ACFFormBlocks\Template;

$form = new Form( $block );

// Start with a basic template.
$template = ( new Template() )
	->add( ( new Block( 'acf/input' ) ) )
	->add( ( new Block( 'acf/submit', [ 'lock' => [ 'move' => false, 'remove' => true ] ] ) ) );

$inner = [
	'template' => $template->get(),
];
?>
<?php if ( $form->get_submission()->is_success() ) : ?>
	<div <?php block_attrs( $block, 'form-placeholder' ); ?>>
		<?php $form->get_confirmation()->render(); ?>
	</div>
<?php else : ?>
	<form
		method="<?php echo esc_attr( $form->get_method() ); ?>"
		action="#<?php echo esc_attr( get_block_id( $block ) ); ?>"
		<?php block_attrs( $block ); ?>
	>
		<?php $form->get_validation()->render(); ?>
		<input
			type="hidden"
			name="<?php echo esc_attr( Form::HIDDEN_FORM_ID ); ?>"
			value="<?php echo esc_attr( get_block_id( $block ) ); ?>"
		/>

		<?php inner_blocks( $inner ); ?>
	</form>
<?php endif; ?>

