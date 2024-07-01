<?php
/**
 * Block: Form
 *
 * @global array $block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Block;
use ACFFormBlocks\Template;

$method = get_field( 'method' ) ?: 'post';

// Start with a basic template.
$template = ( new Template() )
	->add( ( new Block( 'acf/input' ) ) )
	->add( ( new Block( 'acf/submit' ) ) );

$inner = [
	'template' => $template->get(),
]
?>
<form
	method="<?php echo esc_attr( $method ); ?>"
	action="#<?php echo esc_attr( get_block_id( $block ) ); ?>"
	<?php block_attrs( $block ); ?>
>
	<input type="hidden" name="acffb_form_id" value="<?php echo esc_attr( get_block_id( $block ) ); ?>" />
	<?php inner_blocks( $inner ); ?>
</form>
