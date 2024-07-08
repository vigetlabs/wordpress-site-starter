<?php
/**
 * Block: Form
 *
 * @global array $block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Utilities\BlockTemplate\Block;
use ACFFormBlocks\Utilities\BlockTemplate\Template;

$form = acffb_get_form( $block );

wp_enqueue_script( 'acfformblocks-conditional-logic' );

// Start with a basic template.
$template = ( new Template() )
	->add( ( new Block( 'acf/input' ) ) )
	->add( ( new Block( 'acf/submit', [ 'lock' => [ 'move' => false, 'remove' => true ] ] ) ) );

$inner = [
	'template' => $template->get(),
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

