<?php
/**
 * Block: Fieldset
 *
 * @global array    $block
 * @global array    $context
 * @global WP_Block $wp_block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Elements\Fieldset;
use ACFFormBlocks\Utilities\BlockTemplate\Block;
use ACFFormBlocks\Utilities\BlockTemplate\Template;

/** @var Fieldset $field */
$field    = Field::factory( $block, $context, $wp_block );
$template = ( new Template() )
	->add( ( new Block( 'acf/legend' ) ) )
	->add( ( new Block( 'core/paragraph', [ 'placeholder' => __( 'Type / to add fields...', 'acf-field-blocks' ) ] ) ) );

$inner = [
	'template' => $template->get(),
];

$classes = 'form-fieldset';

if ( ! empty( $block['is_checkbox_group'] ) ) {
	$classes .= ' type-checkbox-group';
}
?>
<fieldset <?php block_attrs( $block, $classes ); ?>>
	<?php inner_blocks( $inner ); ?>
</fieldset>
