<?php
/**
 * Block: Legend
 *
 * @global array $block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Utilities\BlockTemplate\Block;
use ACFFormBlocks\Utilities\BlockTemplate\Template;

$inner = [
	'template'      => ( new Template( ( new Block( 'core/paragraph', [ 'placeholder' => __( 'Legend...', 'acf-field-blocks' ), 'lock' => 'all' ] ) ) ) )->get(),
	'allowedBlocks' => [ 'core/paragraph' ],
];
?>
<legend <?php block_attrs( $block ); ?>>
	<?php inner_blocks( $inner ); ?>
</legend>
