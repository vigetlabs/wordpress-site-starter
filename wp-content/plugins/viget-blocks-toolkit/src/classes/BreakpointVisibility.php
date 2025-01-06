<?php
/**
 * Breakpoint Visibility Support
 *
 * @package VigetBlocksToolkit
 */

namespace Viget\VigetBlocksToolkit;

/**
 * Breakpoint Visibility Class
 */
class BreakpointVisibility {

	/**
	 * Initialize the class.
	 */
	public function __construct() {
		// Add block breakpoint visibility CSS.
		self::breakpoint_visibility();
	}

	/**
	 * Add breakpoint visibility block attributes.
	 *
	 * @return void
	 */
	private static function breakpoint_visibility(): void {
		add_filter(
			'render_block',
			function ( string $block_content, array $block ): string {
				// Skip if no visibility settings
				if ( is_admin() || empty( $block['attrs']['breakpointVisibility'] ) ) {
					return $block_content;
				}

				$visibility = $block['attrs']['breakpointVisibility'];
				$block_id   = uniqid();
				$attributes = [];

				// Add data-block attribute
				$attributes[]  = sprintf( 'data-block="%s"', esc_attr( $block_id ) );
				$block_content = trim( $block_content );

				// Add standard breakpoint attributes if not using custom
				if ( empty( $visibility['useCustom'] ) ) {
					if ( ! empty( $visibility['desktop'] ) ) {
						$attributes[] = 'data-visibility-desktop="hide"';
					}
					if ( ! empty( $visibility['tablet'] ) ) {
						$attributes[] = 'data-visibility-tablet="hide"';
					}
					if ( ! empty( $visibility['mobile'] ) ) {
						$attributes[] = 'data-visibility-mobile="hide"';
					}
				}

				// Apply attributes to the outermost element
				if ( preg_match( '/^<([a-zA-Z0-9\-]+)([^>]*)>/', $block_content, $matches ) >= 0 ) {
					$block_content = preg_replace(
						'/^<([a-zA-Z0-9\-]+)([^>]*)>/',
						sprintf( '<$1$2 %s>', implode( ' ', $attributes ) ),
						$block_content,
						1
					);
				}

				if ( ! empty( $visibility['useCustom'] ) ) {
					$custom = $visibility['customBreakpoint'];
					$css = self::generate_custom_breakpoint_css(
						$block_id,
						$custom['width'] ?? '768',
						$custom['unit'] ?? 'px',
						$custom['action'] ?? 'hide',
						$custom['mobileFirst'] ?? false
					);

					$block_content .= sprintf(
						"<style>%s</style>\n",
						$css
					);
				}

				return $block_content;
			},
			10,
			2
		);
	}

	/**
	 * Generate custom CSS for the block
	 *
	 * @param string $block_id    Block's Unique ID
	 * @param string $width        Breakpoint width
	 * @param string $unit         CSS unit
	 * @param string $action       'show' or 'hide'
	 * @param bool   $mobile_first Whether to use mobile-first approach
	 *
	 * @return string
	 */
	private static function generate_custom_breakpoint_css(
		string $block_id,
		string $width,
		string $unit,
		string $action,
		bool $mobile_first
	): string {
		$media_query = $mobile_first
			? "@media (min-width: {$width}{$unit})"
			: "@media (max-width: {$width}{$unit})";

		$display         = $action === 'show' ? 'block' : 'none';
		$initial_display = $action === 'show' ? 'none' : 'block';

		return "
			/* Base styles */
			[data-block=\"{$block_id}\"] {
				display: {$initial_display} !important;
			}
			/* Breakpoint-specific styles */
			{$media_query} {
				[data-block=\"{$block_id}\"] {
					display: {$display} !important;
				}
			}
		";
	}
}
