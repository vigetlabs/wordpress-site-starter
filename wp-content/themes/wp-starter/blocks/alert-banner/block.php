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
function alert_banner_dismiss_button(): void {
	$dismiss_button = sprintf(
		'<button class="dismiss" aria-label="%s" title="%s">',
		esc_attr__( 'Dismiss alert banner', 'wp-starter' ),
		esc_attr__( 'Dismiss', 'wp-starter' )
	);
	$dismiss_button .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">';
	$dismiss_button .= '<line x1="18" y1="6" x2="6" y2="18"></line>';
	$dismiss_button .= '<line x1="6" y1="6" x2="18" y2="18"></line>';
	$dismiss_button .= '</svg>';
	$dismiss_button .= '</button>';

	echo wp_kses_post( $dismiss_button );
}
