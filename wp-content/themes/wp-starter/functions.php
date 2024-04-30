<?php
/**
 * Theme functions
 *
 * @package WPStarter
 */

// Maybe Load Composer dependencies.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

// Init Vite.
require_once get_stylesheet_directory() . '/inc/class-vite.php';
if ( class_exists( 'Vite' ) ) {
	new Vite();
}

// Maybe Initialize Timber.
if ( class_exists( 'Timber\Timber' ) ) {
	Timber\Timber::init();
}

// Pattern Functions.
require_once get_stylesheet_directory() . '/inc/patterns.php';

// Block Functions.
require_once get_stylesheet_directory() . '/inc/blocks.php';
