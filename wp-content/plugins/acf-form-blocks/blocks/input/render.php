<?php
/**
 * Block: Input
 *
 * @global array $block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Block;
use ACFFormBlocks\Template;

$required = get_field( 'required' );
$type = get_field( 'input_type' ) ?: 'text';

$inner = [
	'template' => ( new Template( new Block( 'core/paragraph', [ 'placeholder' => __( 'Field Label...', 'acf-form-blocks' ) ] ) ) )->get(),
];
?>
<div class="form-input">
	<label<?php
		if ( ! is_admin() ) :
			printf( ' for="%s"', esc_attr( get_block_id( $block ) ) );
		endif;
	?>>
		<?php inner_blocks( $inner ); ?>
		<?php if ( $required ) : ?>
			<span class="is-required">*</span>
		<?php endif; ?>
	</label>
	<input type="<?php echo esc_attr( $type ); ?>" name="<?php echo esc_attr( $block['id'] ); ?>" <?php block_attrs( $block ); ?> />
</div>
