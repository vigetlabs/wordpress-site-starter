<?php
/**
 * Block: Fieldset
 *
 * @global array $block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Utilities\BlockTemplate\Block;
use ACFFormBlocks\Utilities\BlockTemplate\Template;

$template = ( new Template() )
	->add( ( new Block( 'acf/legend' ) ) )
	->add( ( new Block( 'core/paragraph', [ 'placeholder' => __( 'Type / to add fields...', 'acf-field-blocks' ) ] ) ) );

$inner = [
	'template' => $template->get(),
];
?>
<fieldset <?php block_attrs( $block ); ?>>
	<?php inner_blocks( $inner ); ?>
</fieldset>
