<?php
/**
 * Block: Dialog
 *
 * @global array $block
 *
 * @package WPStarter
 */

$cbx_id      = uniqid();
$button_text = get_field( 'button_text' );
?>
<?php if ( is_admin() ) : ?>
	<input type="checkbox" id="<?php echo esc_attr( $cbx_id ); ?>" class="acfbt-dialog-checkbox">
	<label for="<?php echo esc_attr( $cbx_id ); ?>" class="acfbt-dialog-label"><?php esc_html_e( 'Toggle Dialog', 'wp-site-starter' ); ?></label>
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
			class="acf-dialog-close"
			@click="handleDialogClose()"
		>
			<span class="sr-only"><?php esc_html_e( 'Close', 'wp-site-starter' ); ?></span>
		</button>
	<?php endif; ?>

	<div class="inner">
		<?php if ( is_admin() ) : ?>
			<label for="<?php echo esc_attr( $cbx_id ); ?>" class="acfbt-dialog-close">
				<span class="sr-only"><?php esc_html_e( 'Close', 'wp-site-starter' ); ?></span>
			</label>
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
	<?php esc_html_e( $button_text ? $button_text : 'Open', 'wp-site-starter' ); ?>
</button>
</div>
