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

/** @var Fieldset $field */
$field = Field::factory( $block, $context, $wp_block );
$inner = [
	'template' => $field->get_template(),
];

$classes = 'form-fieldset';

if ( $field->is_checkbox_group() ) {
	$classes .= ' type-checkbox-group';
}
?>
<fieldset <?php acffb_block_attrs( $field, $classes ); ?>>
	<?php inner_blocks( $inner ); ?>
</fieldset>
