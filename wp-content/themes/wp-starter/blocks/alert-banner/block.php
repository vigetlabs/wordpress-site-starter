<?php
/**
 * Block: Alert Banner
 *
 * @package WPStarter
 */

// Register Pattern Category.
add_action(
	'init',
	function () {
		register_block_pattern_category(
			'alert-banner',
			[
				'label' => __( 'Alert Banner', 'wp-starter' ),
			]
		);
	}
);

/**
 * Alert Banner Dismiss Button
 *
 * @param string $id Alert Banner ID
 *
 * @return void
 */
function alert_banner_dismiss_button( string $id ): void {
	$dismiss_button = sprintf(
		'<button class="dismiss" aria-label="%1$s" title="%2$s" @click="%3$s = !%3$s">',
		esc_attr__( 'Dismiss alert banner', 'wp-starter' ),
		esc_attr__( 'Dismiss', 'wp-starter' ),
		esc_attr( $id )
	);
	$dismiss_button .= '</button>';

	echo $dismiss_button;
}
