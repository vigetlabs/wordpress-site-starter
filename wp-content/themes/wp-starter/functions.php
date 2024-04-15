<?php
/**
 * WP Starter functions
 *
 * @package WPStarter
 */

add_action(
	'after_setup_theme',
	function () {
		remove_theme_support( 'core-block-patterns' );
	}
);
