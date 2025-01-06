<?php
/**
 * Block: Textarea
 *
 * @global array $block
 * @global array $context
 * @global WP_Block $wp_block
 *
 * @package VigetFormBlocks
 */

use VigetFormBlocks\Elements\Field;
use VigetFormBlocks\Elements\Textarea;

/** @var Textarea $field */
$field = Field::factory( $block, $context, $wp_block );

$inner = [
	'template' => $field->get_template(),
];
?>
<div class="form-input type-textarea">
	<?php inner_blocks( $inner ); ?>

	<textarea <?php block_attrs( $block, '', $field->get_attrs() ); ?>><?php echo esc_textarea( $field->get_value() ); ?></textarea>
</div>
