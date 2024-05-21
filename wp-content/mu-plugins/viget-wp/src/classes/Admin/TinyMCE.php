<?php
/**
 * @package VigetWP
 */

namespace VigetWP\Admin;

/**
 * TinyMCE Class
 */
class TinyMCE {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Customize the TinyMCE Toolbar
		$this->customize_toolbar();
	}

	/**
	 * Customize the Toolbar
	 *
	 * @return void
	 */
	private function customize_toolbar(): void {
		add_filter(
			'tiny_mce_before_init',
			function ( array $settings ): array {
				$settings['block_formats'] = 'Heading 2=h2;Heading 3=h3;Heading 4=h4;Paragraph=p';

				// Set to true to include the default settings.
				$settings['style_formats_merge'] = false;

				$style_formats = [
					[
						'title'    => 'Button Default',
						'selector' => 'a',
						'classes'  => 'btn-default',
					],
					[
						'title'    => 'Button Secondary',
						'selector' => 'a',
						'classes'  => 'btn-secondary',
					],
				];

				$settings['style_formats'] = json_encode( $style_formats );

				return $settings;
			}
		);
	}
}
