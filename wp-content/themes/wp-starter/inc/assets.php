<?php
/**
 * Assets
 *
 * @package WPStarter
 */

namespace WPStarter;

// Use Vite to enqueue assets and dependencies.
add_action(
	'init',
	function () {
		$vite = Vite::get_instance();

		// Webfont Example.
		// wp_register_style(
		// 	'wp-starter-typekit',
		// 	'https://use.typekit.net/o0oOoo0.css',
		// 	[],
		// 	wp_get_theme()->get( 'Version' )
		// );

		// $vite->add_css_dependency( 'wp-starter-typekit' );
	}
);
