<?php
/**
 * Block: Submit
 *
 * @global array $block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Block;
use ACFFormBlocks\Template;

$buttons = ( new Block( 'core/buttons' ) )
	->add( ( new Block( 'core/button', [ 'text' => __( 'Submit', 'acf-form-blocks' ) ] ) ) );
$inner = [
	'template' => ( new Template( $buttons ) )->get(),
];
?>
<footer class="form-submit">
	<?php inner_blocks( $inner ); ?>
</footer>
