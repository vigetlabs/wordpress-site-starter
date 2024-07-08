<?php
/**
 * Block: Radios
 *
 * @global array $block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Elements\Radios;
use ACFFormBlocks\Utilities\BlockTemplate\Block;
use ACFFormBlocks\Utilities\BlockTemplate\Template;

/** @var Radios $field */
$field   = Field::factory( $block );
$options = $field->get_options();
$inner   = [
	'template' => ( new Template( new Block( 'core/paragraph', [ 'placeholder' => __( 'Field Label...', 'acf-form-blocks' ) ] ) ) )->get(),
];

$has_checked = false;
?>
<div <?php block_attrs( $block, 'form-input type-radios' ); ?>>
	<div class="radios-label">
		<?php inner_blocks( $inner ); ?>
		<?php if ( $field->is_required() ) : ?>
			<span class="is-required">*</span>
		<?php endif; ?>
	</div>

	<?php if ( empty( $options ) ) : ?>
		<p><?php esc_html_e( 'No options available.', 'acf-form-blocks' ); ?></p>
	<?php else : ?>
		<ul class="radios-options">
			<?php
			foreach ( $options as $index => $option ) :
				$option_id = $field->get_id() . '-' . $index;
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
						name="<?php echo esc_attr( $field->get_name() ); ?>"
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
