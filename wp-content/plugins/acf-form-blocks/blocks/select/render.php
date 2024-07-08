<?php
/**
 * Block: Select
 *
 * @global array $block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Elements\Select;
use ACFFormBlocks\Utilities\BlockTemplate\Block;
use ACFFormBlocks\Utilities\BlockTemplate\Template;

/** @var Select $field */
$field   = Field::factory( $block );
$options = $field->get_options();
$inner   = [
	'template' => ( new Template( new Block( 'core/paragraph', [ 'placeholder' => __( 'Field Label...', 'acf-form-blocks' ) ] ) ) )->get(),
];

$has_selected = false;
?>
<div class="form-input type-select">
	<label<?php
		if ( ! is_admin() ) :
			printf( ' for="%s"', esc_attr( $field->get_id() ) );
		endif;
	?>>
		<?php inner_blocks( $inner ); ?>
		<?php if ( $field->is_required() ) : ?>
			<span class="is-required">*</span>
		<?php endif; ?>
	</label>

	<select <?php block_attrs( $block ); ?>>
		<?php if ( empty( $options ) ) : ?>
			<option value="" selected><?php esc_html_e( 'No options available.', 'acf-form-blocks' ); ?></option>
		<?php else : ?>
			<?php
			foreach ( $options as $option ) :
				$selected = ! $has_selected ? selected( $option['value'], $field->get_value(), false ) : '';
				if ( $selected ) :
					$has_selected = true;
				endif;
				?>
				<option value="<?php echo esc_attr( $option['value'] ); ?>" <?php echo $selected; ?>><?php echo esc_html( $option['label'] ); ?></option>
			<?php endforeach;
		endif;
		?>
	</select>
</div>
