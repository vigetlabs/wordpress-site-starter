<?php
/**
 * Custom Button Icons.
 *
 * @package WPStarter
 */

/**
 * Add custom icons to the button icons array.
 * */

add_filter(
	'acfbt_button_icons',
	function ( array $icons ): array {
		$icon_path = get_stylesheet_directory() . '/src/images/icons/';

		/* Custom Icons */
		/*
		$icons['short-arrow-right'] = [
			'label'       => __( 'YOUR LABEL', 'wp-starter' ),
			'icon'        => file_get_contents( $icon_path . 'file-name.svg' ),
			'defaultLeft' => false,
		];*/

		return $icons;
	}
);
