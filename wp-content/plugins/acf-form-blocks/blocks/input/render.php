<?php
/**
 * Block: Input
 *
 * @global array $block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Elements\Input;
use ACFFormBlocks\Utilities\BlockTemplate\Block;
use ACFFormBlocks\Utilities\BlockTemplate\Template;

/** @var Input $field */
$field = Field::factory( $block );
$inner = [
	'template' => ( new Template( new Block( 'core/paragraph', [ 'placeholder' => __( 'Field Label...', 'acf-form-blocks' ) ] ) ) )->get(),
];
?>
<div class="form-input type-input input-type-<?php echo esc_attr( $field->get_input_type() ); ?>">
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

	<input <?php block_attrs( $block ); ?> />
</div>
