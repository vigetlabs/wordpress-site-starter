<?php
/**
 * Block: Legend
 *
 * @global array $block
 * @global array $context
 * @global WP_Block $wp_block
 *
 * @package VigetFormBlocks
 */

use VigetFormBlocks\Elements\Field;
use VigetFormBlocks\Elements\Legend;

/** @var Legend $field */
$field = Field::factory( $block, $context, $wp_block );

$fieldset  = $field->get_fieldset();
$required  = $fieldset?->is_required() ?? false;
$placement = $fieldset?->get_marker_placement() ?? 'after';

$inner = [
	'template'      => $field->get_template(),
	'allowedBlocks' => [ 'core/paragraph' ],
];
?>
<legend <?php block_attrs( $block, '', $field->get_attrs() ); ?>>
	<?php if ( $required && 'before' === $placement ) : ?>
		<span class="is-required">*</span>
	<?php endif; ?>

	<?php inner_blocks( $inner ); ?>

	<?php if ( $required && 'after' === $placement ) : ?>
		<span class="is-required">*</span>
	<?php endif; ?>
</legend>
