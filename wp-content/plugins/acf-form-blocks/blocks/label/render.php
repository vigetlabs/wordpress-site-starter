<?php
/**
 * Block: Label
 *
 * @global array $block
 * @global array $context
 * @global WP_Block $wp_block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Elements\Label;

/** @var Label $field */
$field = Field::factory( $block, $context, $wp_block );

$parent    = $field->get_parent_field();
$required  = $parent?->is_required() ?? false;
$placement = $parent?->get_marker_placement() ?? 'before';

$inner = [
	'template'      => $field->get_template(),
	'allowedBlocks' => [ 'core/paragraph' ],
];
?>
<label <?php block_attrs( $block, '', $field->get_attrs() ); ?>>
	<?php if ( $required && 'before' === $placement ) : ?>
		<span class="is-required">*</span>
	<?php endif; ?>

	<?php inner_blocks( $inner ); ?>

	<?php if ( $required && 'after' === $placement ) : ?>
		<span class="is-required">*</span>
	<?php endif; ?>
</label>
