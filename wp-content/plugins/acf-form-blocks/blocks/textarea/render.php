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
use ACFFormBlocks\Elements\Textarea;

/** @var Textarea $field */
$field = Field::factory( $block, $context, $wp_block );

$inner = [
	'template' => $field->get_template(),
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

	<textarea <?php block_attrs( $block, '', $field->get_attrs() ); ?>><?php echo esc_textarea( $field->get_value() ); ?></textarea>
</div>
