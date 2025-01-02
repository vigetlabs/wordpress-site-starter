<?php
/**
 * Block: Submit
 *
 * @global array $block
 *
 * @package ACFFormBlocks
 */

use Viget\ACFBlocksToolkit\BlockTemplate\Block;
use Viget\ACFBlocksToolkit\BlockTemplate\Template;

$buttons = ( new Block( 'core/buttons', [ 'lock' => [ 'move' => false, 'remove' => true ] ] ) )
	->add( ( new Block( 'core/button', [
		'text' => __( 'Submit', 'acf-form-blocks' ),
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
