<?php
/**
 * WP Starter functions
 *
 * @package WPStarter
 */

// Maybe Load Composer dependencies.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

// Maybe Initialize Timber.
if ( class_exists( 'Timber\Timber' ) ) {
	Timber\Timber::init();
}

add_action(
	'after_setup_theme',
	function () {
		remove_theme_support( 'core-block-patterns' );
	}
);

add_filter(
	'block_categories_all',
	function ( array $categories ): array {
		array_unshift(
			$categories,
			[
				'slug'  => 'components',
				'title' => __( 'Components', 'wp-starter' ),
			]
		);
		return $categories;
	}
);

add_action(
	'init',
	function () {
		register_block_pattern_category(
			'cta',
			[
				'label' => __( 'Call To Action', 'wp-starter' ),
			]
		);
	}
);
