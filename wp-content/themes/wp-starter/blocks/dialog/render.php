<?php
/**
 * Block: Dialog
 *
 * @global array $block
 *
 * @package WPStarter
 */

$cbx_id = uniqid();
?>
<?php if ( is_admin() ) : ?>
	<input type="checkbox" id="<?php echo esc_attr( $cbx_id ); ?>" class="acfbt-dialog-checkbox">
	<label for="<?php echo esc_attr( $cbx_id ); ?>" class="acfbt-dialog-label"><?php esc_html_e( 'Toggle', 'wp-site-starter' ); ?></label>
<?php endif; ?>

<dialog <?php block_attrs( $block ); ?>>
	<?php if ( ! is_admin() ) : ?>
<!--		<button>--><?php //esc_html_e( 'Close', 'wp-site-starter' ); ?><!--</button>-->
	<?php endif; ?>

	<div class="inner">
		<?php if ( is_admin() ) : ?>
			<label for="<?php echo esc_attr( $cbx_id ); ?>" class="acfbt-dialog-close"><?php esc_html_e( 'Close', 'wp-site-starter' ); ?></label>
		<?php endif; ?>
		<?php inner_blocks(); ?>
	</div>
</dialog>

<button><?php esc_html_e( 'Open', 'wp-site-starter' ); ?></button>
