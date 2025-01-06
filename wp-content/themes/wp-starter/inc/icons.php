<?php
/**
 * Custom Button Icons.
 *
 * @package WPStarter
 */

/**
 * Add custom icons to the button icons array.
 * Place your custom icon(s) in the `src/images/icons` directory.
 * Then add the icon(s) to the array, the key and label should be unique for each icon.
 */

add_filter(
	'vgtbt_button_icons',
	function ( array $icons ): array {
		$icon_path = get_stylesheet_directory() . '/src/images/icons/';

		/* Remove unused default icons */
		// unset( $icons['wordpress'] );

		return array_merge([
			/* Custom Icons */
			/* Be sure icon fill color is set to `currentColor` */
			/*
			'our-custom-icon'  => [
				'label'       => __( 'your custom icon', 'wp-starter' ),
				'icon'        => file_get_contents( $icon_path . 'file-name.svg' ),
				'defaultLeft' => false,
			],
			*/
		], $icons );
	}
);
