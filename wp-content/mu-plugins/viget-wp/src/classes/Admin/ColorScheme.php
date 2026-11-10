<?php
/**
 * Admin Color Scheme Customization
 *
 * @since 1.0.0
 *
 * @package VigetWP
 */

namespace VigetWP\Admin;

/**
 * ColorScheme Class
 */
class ColorScheme {

	/**
	 * Admin colors. Output as --vigetwp-color-* CSS variables.
	 *
	 * @var array
	 */
	const COLORS = [
		'primary'   => '#1a4155', // Admin menu, buttons, links, block editor.
		'secondary' => '#265f7c', // Hover states, submenus.
		'accent'    => '#f26628', // Current menu item, notification bubbles.
		'text'      => '#ffffff', // Admin menu text.
	];

	/**
	 * Constructor
	 */
	public function __construct() {
		// Set the Admin color scheme
		$this->set_color_scheme();

		// Default to the custom color scheme
		$this->default_user_color_scheme();

		// Output the color variables
		$this->color_variables();

		// Set the TinyMCE color scheme
		$this->set_tinymce_color_scheme();

		// Share the colors with Viget Parts Kit
		$this->parts_kit_colors();
	}

	/**
	 * Get the admin colors
	 *
	 * @return array
	 */
	public static function colors(): array {
		return array_merge( self::COLORS, (array) apply_filters( 'vigetwp_admin_colors', self::COLORS ) );
	}

	/**
	 * Get the color variables CSS
	 *
	 * @return string
	 */
	public static function css_variables(): string {
		$css = '';

		foreach ( self::colors() as $name => $color ) {
			$css .= sprintf( '--vigetwp-color-%s:%s;', $name, $color );
		}

		return ':root{' . $css . '}';
	}

	/**
	 * Set the Admin Color Scheme
	 *
	 * @return void
	 */
	private function set_color_scheme(): void {
		add_action(
			'admin_init',
			function () {
				$colors = self::colors();

				wp_admin_css_color(
					get_template(),
					get_bloginfo( 'name' ),
					VIGETWP_PLUGIN_URL . 'src/assets/css/admin-style.css',
					[
						$colors['primary'],
						$colors['text'],
						$colors['accent'],
						$colors['accent'],
					]
				);
			}
		);
	}

	/**
	 * Default to the custom color scheme
	 *
	 * @return void
	 */
	private function default_user_color_scheme(): void {
		add_filter(
			'get_user_option_admin_color',
			fn() => get_template(),
			5
		);
	}

	/**
	 * Output the color variables, and point core's theme color at the primary color.
	 *
	 * @return void
	 */
	private function color_variables(): void {
		add_action(
			'admin_enqueue_scripts',
			function () {
				$primary = self::colors()['primary'];

				$css  = self::css_variables();
				$css .= sprintf(
					'body.admin-color-%1$s{--wp-admin-theme-color:%2$s;--wp-admin-theme-color--rgb:%3$s;--wp-admin-theme-color-darker-10:color-mix(in srgb,%2$s,#000 5%%);--wp-admin-theme-color-darker-20:color-mix(in srgb,%2$s,#000 10%%);}',
					get_template(),
					$primary,
					implode( ',', sscanf( $primary, '#%02x%02x%02x' ) )
				);

				wp_register_style( 'vigetwp-colors', false, [], vigetwp()->get_version() );
				wp_enqueue_style( 'vigetwp-colors' );
				wp_add_inline_style( 'vigetwp-colors', $css );
			}
		);
	}

	/**
	 * Set the TinyMCE color scheme
	 *
	 * @return void
	 */
	private function set_tinymce_color_scheme(): void {
		add_action(
			'admin_init',
			function () {
				add_editor_style( VIGETWP_PLUGIN_URL . 'src/assets/css/admin-editor.css' );
			}
		);

		add_filter(
			'tiny_mce_before_init',
			function ( array $settings ): array {
				$settings['content_style'] = ( $settings['content_style'] ?? '' ) . self::css_variables();
				return $settings;
			}
		);
	}

	/**
	 * Share the colors with Viget Parts Kit
	 *
	 * @return void
	 */
	private function parts_kit_colors(): void {
		add_filter(
			'vgtpk_admin_colors',
			fn( array $colors ): array => array_merge(
				$colors,
				[
					'scheme'  => get_template(),
					'base'    => self::colors()['primary'],
					'primary' => self::colors()['primary'],
					'accent'  => self::colors()['accent'],
				]
			)
		);
	}
}
