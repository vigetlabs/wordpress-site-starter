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
use ACFFormBlocks\Utilities\BlockTemplate\Block;
use ACFFormBlocks\Utilities\BlockTemplate\Template;

$field = Field::factory( $block, $context, $wp_block );

$fieldset = $field->get_fieldset();
$required = $fieldset?->is_required() ?? false;

$inner = [
	'template'      => ( new Template( ( new Block( 'core/paragraph', [ 'placeholder' => __( 'Legend...', 'acf-field-blocks' ), 'lock' => 'all' ] ) ) ) )->get(),
	'allowedBlocks' => [ 'core/paragraph' ],
];
?>
<legend <?php acffb_block_attrs( $field ); ?>>
	<?php inner_blocks( $inner ); ?>
	<?php if ( $required ) : ?>
		<span class="is-required">*</span>
	<?php endif; ?>
</legend>
