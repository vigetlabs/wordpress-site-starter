<?php
/**
 * Theme functions
 *
 * @package WPStarter
 */

namespace WPStarter;

// Maybe Load Composer dependencies.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

// Init Vite.
require_once get_stylesheet_directory() . '/inc/class-vite.php';
if ( class_exists( __NAMESPACE__ . '\Vite' ) ) {
	Vite::get_instance();
}

// Maybe Initialize Timber.
if ( class_exists( '\Timber\Timber' ) ) {
	\Timber\Timber::init();
}

// Block Functions.
require_once get_stylesheet_directory() . '/inc/blocks.php';

// Custom Icons.
require_once get_stylesheet_directory() . '/inc/icons.php';

// Webfont.
// add_action( 'wp_enqueue_scripts', function () {
// 	wp_enqueue_style(
// 		'THEME-adobe-fonts',
// 		'https://use.typekit.net/xxxxxxx.css',
// 		[],
// 		null
// 	);
// } );
