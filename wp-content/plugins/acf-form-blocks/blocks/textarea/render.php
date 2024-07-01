<?php
/**
 * Block: Textarea
 *
 * @global array $block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Block;
use ACFFormBlocks\Template;

$required = get_field( 'required' );

$inner = [
	'template' => ( new Template( new Block( 'core/paragraph', [ 'placeholder' => __( 'Field Label...', 'acf-form-blocks' ) ] ) ) )->get(),
];
?>
<div class="form-input type-textarea">
	<label<?php
		if ( ! is_admin( $inner ) ) :
			printf( ' for="%s"', esc_attr( get_block_id( $block ) ) );
		endif;
	?>>
		<?php inner_blocks(); ?>
		<?php if ( $required ) : ?>
			<span class="is-required">*</span>
		<?php endif; ?>
	</label>
	<textarea name="textarea" name="<?php echo esc_attr( $block['id'] ); ?>" <?php block_attrs( $block ); ?>></textarea>
</div>
