<?php
/**
 * Block: Fieldset
 *
 * @global array    $block
 * @global array    $context
 * @global WP_Block $wp_block
 *
 * @package VigetFormBlocks
 */

use VigetFormBlocks\Elements\Field;
use VigetFormBlocks\Elements\Fieldset;

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
<fieldset <?php block_attrs( $block, $classes, $field->get_attrs() ); ?>>
	<?php inner_blocks( $inner ); ?>
</fieldset>
