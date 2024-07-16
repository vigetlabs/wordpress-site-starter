<?php
/**
 * Block: Textarea
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

$inner = [
	'template' => ( new Template( new Block( 'core/paragraph', [ 'placeholder' => __( 'Field Label...', 'acf-form-blocks' ) ] ) ) )->get(),
];
?>
<div class="form-input type-textarea">
	<label<?php
		if ( ! is_admin() ) :
			printf( ' for="%s"', esc_attr( $field->get_id_attr() ) );
		endif;
	?>>
		<?php inner_blocks( $inner ); ?>
		<?php if ( $field->is_required() ) : ?>
			<span class="is-required">*</span>
		<?php endif; ?>
	</label>

	<textarea <?php acffb_block_attrs( $field ); ?>><?php echo esc_textarea( $field->get_value() ); ?></textarea>
</div>
