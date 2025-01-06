<?php
/**
 * Block: Submit
 *
 * @global array $block
 *
 * @package VigetFormBlocks
 */

use Viget\VigetBlocksToolkit\BlockTemplate\Block;
use Viget\VigetBlocksToolkit\BlockTemplate\Template;

$buttons = ( new Block( 'core/buttons', [ 'lock' => [ 'move' => false, 'remove' => true ] ] ) )
	->add( ( new Block( 'core/button', [
		'text' => __( 'Submit', 'viget-form-blocks' ),
		'lock' => [ 'move' => false, 'remove' => true ],
	] ) ) );

$inner = [
	'template' => ( new Template( $buttons ) )->get(),
];

// TODO: Set up form footer as separate component.
?>
<footer <?php block_attrs( $block, 'form-submit' ); ?>>
	<?php inner_blocks( $inner ); ?>
</footer>
