<?php
/**
 * Block: Legend
 *
 * @global array $block
 * @global array $context
 * @global WP_Block $wp_block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Elements\Legend;

/** @var Legend $field */
$field = Field::factory( $block, $context, $wp_block );

$fieldset = $field->get_fieldset();
$required = $fieldset?->is_required() ?? false;

$inner = [
	'template'      => $field->get_template(),
	'allowedBlocks' => [ 'core/paragraph' ],
];
?>
<legend <?php block_attrs( $block, '', $field->get_attrs() ); ?>>
	<?php inner_blocks( $inner ); ?>
	<?php if ( $required ) : ?>
		<span class="is-required">*</span>
	<?php endif; ?>
</legend>