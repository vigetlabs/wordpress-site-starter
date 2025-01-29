<?php
/**
 * Block: Alert Banner
 *
 * @package WPStarter
 */

namespace WPStarter\AlertBanner;

/**
 * Alert Banner Dismiss Button
 *
 * @param string $id Alert Banner ID.
 *
 * @return void
 */
function dismiss_button( string $id ): void {
	printf(
		'<button class="dismiss" aria-label="%1$s" title="%2$s" @click="%3$s = !%3$s"></button>',
		esc_attr__( 'Dismiss alert banner', 'wp-starter' ),
		esc_attr__( 'Dismiss', 'wp-starter' ),
		esc_attr( $id )
	);
}

/**
 * Get the ID for the Alert Banner.
 *
 * @param array $block The block array.
 *
 * @return string
 */
function get_id( array $block ): string {
	return str_replace( '-', '_', get_block_id( $block ) );
}

// Make functions available in Twig.
add_filter(
	'timber/twig/functions',
	function ( array $functions ) {
		$functions['wpstarter_alertbanner_dismiss_button'] = [
			'callable' => '\\WPStarter\\AlertBanner\\dismiss_button',
		];

		$functions['wpstarter_alertbanner_get_id'] = [
			'callable' => '\\WPStarter\\AlertBanner\\get_id',
		];

		return $functions;
	}
);
