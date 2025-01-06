<?php
/**
 * Default Block Template
 *
 * @global array $block
 *
 * @package VigetBlocksToolkit
 */

?>
<section <?php block_attrs( $block ); ?>>
	<p style="text-align:center">
		<?php esc_html_e( 'Default Block template', 'viget-blocks-toolkit' ); ?>:
		<?php echo esc_html( $block['slug'] ); ?>
	</p>
</section>

<?php if ( ! empty( $block['data'] ) ) : ?>
	<p><strong><?php esc_html_e( 'Block data available', 'viget-blocks-toolkit' ); ?>:</strong></p>
	<pre>
		<?php var_dump( $block['data'] ); ?>
	</pre>
	<?php
endif;
