<?php
/**
 * Pattern Functions
 *
 * @package WPStarter
 */

// Remove core block patterns.
add_action(
	'after_setup_theme',
	function () {
		remove_theme_support( 'core-block-patterns' );
	}
);

// Register Pattern Categories.
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
