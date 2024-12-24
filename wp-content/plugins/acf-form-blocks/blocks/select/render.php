<?php
/**
 * Block: Select
 *
 * @global array $block
 * @global array $context
 * @global WP_Block $wp_block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Elements\Select;

/** @var Select $field */
$field   = Field::factory( $block, $context, $wp_block );
$options = $field->get_options();
$inner   = [
	'template' => $field->get_template(),
];

$has_selected = false;
?>
<div class="form-input type-select">

	<?php inner_blocks( $inner ); ?>

	<select <?php block_attrs( $block, '', $field->get_attrs() ); ?>>
		<?php if ( empty( $options ) ) : ?>
			<option value="" selected><?php esc_html_e( 'No options available.', 'acf-form-blocks' ); ?></option>
		<?php else : ?>
			<?php
			foreach ( $options as $option ) :
				$is_selected = is_array( $field->get_value() ) ? in_array( $option['value'], $field->get_value(), true ) : $option['value'] === $field->get_value();
				$selected    = ! $has_selected || $field->is_multiple() ? selected( $is_selected, true, false ) : '';
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
