<?php
/**
 * Block: Alert Banner
 *
 * @package WPStarter
 */

/**
 * Alert Banner Dismiss Button
 *
 * @return void
 */
function alert_banner_dismiss_button($id = 'alert-banner'): void {
	$dismiss_button = sprintf(
		'<button class="dismiss" aria-label="%s" title="%s" @click="%s = !%s">',
		esc_attr__( 'Dismiss alert banner', 'wp-starter' ),
		esc_attr__( 'Dismiss', 'wp-starter' ),
		$id,
		$id
	);
	$dismiss_button .= '</button>';

	echo ( $dismiss_button );
}
