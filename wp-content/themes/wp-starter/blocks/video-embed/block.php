<?php
/**
 * Video Embed Block
 *
 * @package WPStarter
 */

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'responsive-embeds' );
	}
);
