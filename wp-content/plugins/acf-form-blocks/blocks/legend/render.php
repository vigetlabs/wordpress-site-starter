<?php
/**
 * Block: Legend
 *
 * @global array $block
 * @global array $context
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Elements\Legend;
use ACFFormBlocks\Utilities\BlockTemplate\Block;
use ACFFormBlocks\Utilities\BlockTemplate\Template;

/** @var Legend $field */
$field = Field::factory( $block, $context );

$fieldset = $field->get_fieldset();
$required = $fieldset?->is_required() ?? false;

$inner = [
	'template'      => ( new Template( ( new Block( 'core/paragraph', [ 'placeholder' => __( 'Legend...', 'acf-field-blocks' ), 'lock' => 'all' ] ) ) ) )->get(),
	'allowedBlocks' => [ 'core/paragraph' ],
];
?>
<legend <?php block_attrs( $block ); ?>>
	<?php inner_blocks( $inner ); ?>
	<?php if ( $required ) : ?>
		<span class="is-required">*</span>
	<?php endif; ?>
</legend>
