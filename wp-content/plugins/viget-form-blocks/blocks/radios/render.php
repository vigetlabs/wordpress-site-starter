<?php
/**
 * Block: Radios
 *
 * @global array $block
 * @global array $context
 * @global WP_Block $wp_block
 *
 * @package VigetFormBlocks
 */

use VigetFormBlocks\Elements\Field;
use VigetFormBlocks\Elements\Radios;

/** @var Radios $field */
$field   = Field::factory( $block, $context, $wp_block );
$options = $field->get_options();
$inner   = [
	'template' => $field->get_template(),
];

$has_checked = false;
?>
<div <?php block_attrs( $block, 'form-input type-radios', $field->get_attrs() ); ?>>
	<?php inner_blocks( $inner ); ?>

	<?php if ( empty( $options ) ) : ?>
		<p><?php esc_html_e( 'No options available.', 'viget-form-blocks' ); ?></p>
	<?php else : ?>
		<ul class="radios-options">
			<?php
			foreach ( $options as $index => $option ) :
				$option_id = $field->get_id_attr() . '-' . $index;
				$checked   = ! $has_checked ? checked( $option['value'], $field->get_value(), false ) : '';
				if ( $checked ) :
					$has_checked = true;
				endif;
				?>
				<li>
					<input
						type="radio"
						value="<?php echo esc_attr( $option['value'] ); ?>"
						id="<?php echo esc_attr( $option_id ); ?>"
						name="<?php echo esc_attr( $field->get_name_attr() ); ?>"
						<?php echo $checked; ?>
					>
					<label for="<?php echo esc_attr( $option_id ); ?>">
						<?php echo esc_html( $option['label'] ); ?>
					</label>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
