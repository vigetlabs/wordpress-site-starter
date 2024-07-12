<?php
/**
 * Block: Checkbox
 *
 * @global array $block
 * @global array $context
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Utilities\BlockTemplate\Block;
use ACFFormBlocks\Utilities\BlockTemplate\Template;
use ACFFormBlocks\Utilities\Cache;

$field = Field::factory( $block, $context );
$inner = [
	'template' => (
		new Template(
			new Block(
				'core/paragraph',
				[ 'placeholder' => __( 'Label...', 'acf-form-blocks' ) ]
			)
		)
	)->get(),
];

$custom_value = get_field( 'checkbox_value' );
$value        = get_field( 'value' ) ?? 1;

if ( ! $custom_value ) {
	$form = Cache::get( $context['acffb/form_id'] );
	$checkbox = $form?->get_form_object()->get_field_by_id( $field->get_id() );
	$value    = $checkbox?->get_label() ? $checkbox?->get_label() : $value;
}
?>
<div <?php block_attrs( $block, 'form-input type-checkbox' ); ?>>
	<input
		type="checkbox"
		value="<?php echo esc_attr( $value ); ?>"
		id="<?php echo esc_attr( $field->get_id_attr() ); ?>"
		name="<?php echo esc_attr( $field->get_name() ); ?>"
		<?php checked( $value, $field->get_value() ); ?>
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
