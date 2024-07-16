<?php
/**
 * Block: Checkbox
 *
 * @global array $block
 * @global array $context
 * @global WP_Block $wp_block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Elements\Checkbox;
use ACFFormBlocks\Elements\Field;

/** @var Checkbox $field */
$field = Field::factory( $block, $context, $wp_block );
$inner = [
	'template' => $field->get_template(),
];
?>
<div <?php block_attrs( $block, 'form-input type-checkbox' ); ?>>
	<input
		type="checkbox"
		value="<?php echo esc_attr( $field->get_value() ); ?>"
		id="<?php echo esc_attr( $field->get_id_attr() ); ?>"
		name="<?php echo esc_attr( $field->get_name_attr() ); ?>"
		<?php checked( $field->is_checked() ); ?>
	>
	<label
		<?php if ( ! is_admin() ) : ?>
			for="<?php echo esc_attr( $field->get_id_attr() ); ?>"
		<?php endif; ?>
	>
		<?php if ( $field->is_required() ) : ?>
			<span class="is-required">*</span>
		<?php endif; ?>
		<?php inner_blocks( $inner ); ?>
	</label>
</div>
