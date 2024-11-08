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

<div
<?php if ( ! is_admin() ) : ?>
	x-data="{
	openDialog: false,

	// Close the dialog when the user clicks backdrop
	handleDialogClick(event) {
		(event.target === $refs.dialogRef) && this.handleDialogClose()
	},

	// Delay close to allow for animation
	handleDialogClose() {
		if (!this.openDialog) return

		this.openDialog = false
		$refs.dialogRef.close()
	}
	}"
<?php endif; ?>
>

<dialog
	<?php block_attrs( $block ); ?>
	<?php if ( ! is_admin() ) : ?>
		x-ref="dialogRef"
		@keydown.escape.prevent="handleDialogClose()"
		@click="handleDialogClick(event)"
	<?php endif; ?>
>
	<?php if ( ! is_admin() ) : ?>
		<button
			@click="handleDialogClose()"
		>
			<?php esc_html_e( 'Close', 'wp-site-starter' ); ?>
		</button>
	<?php endif; ?>

	<div class="inner">
		<?php if ( is_admin() ) : ?>
			<label for="<?php echo esc_attr( $cbx_id ); ?>" class="acfbt-dialog-close"><?php esc_html_e( 'Close', 'wp-site-starter' ); ?></label>
		<?php endif; ?>
		<?php inner_blocks(); ?>
	</div>
</dialog>
<button
	class="btn-default"
	<?php if ( ! is_admin() ) : ?>
		@click="$refs.dialogRef.showModal(), openDialog = true"
	<?php endif; ?>
>
	<?php esc_html_e( 'Open', 'wp-site-starter' ); ?>
</button>
</div>
