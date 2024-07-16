<?php
/**
 * Block: Submit
 *
 * @global array $block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Utilities\BlockTemplate\Block;
use ACFFormBlocks\Utilities\BlockTemplate\Template;

$buttons = ( new Block( 'core/buttons', [ 'lock' => [ 'move' => false, 'remove' => true ] ] ) )
	->add( ( new Block( 'core/button', [
		'text' => __( 'Submit', 'acf-form-blocks' ),
		'lock' => [ 'move' => false, 'remove' => true ],
	] ) ) );

$inner = [
	'template' => ( new Template( $buttons ) )->get(),
];
?>
<footer class="form-submit">
	<?php inner_blocks( $inner ); ?>
</footer>
