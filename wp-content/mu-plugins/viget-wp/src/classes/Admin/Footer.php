<?php
/**
 * @package VigetWP
 */

namespace VigetWP\Admin;

/**
 * Modify the admin footer
 */
class Footer {
	/**
	 * Constructor
	 */
	public function __construct() {
		// Customize the Admin Footer Text
		$this->modify_footer_text();
	}

	/**
	 * Modify the admin footer text
	 *
	 * @return void
	 */
	private function modify_footer_text(): void {
		add_filter(
			'admin_footer_text',
			function ( string $footer_text ): string {
				return sprintf(
					'<span id="footer-thankyou">%s ❤️️ %s <a href="https://www.viget.com/" target="_blank" rel="noopener noreferrer">%s</a>.</span>&nbsp;',
					esc_html__( 'Made with', 'viget-wp' ),
					esc_html__( 'by', 'viget-wp' ),
					esc_html__( 'Viget', 'viget-wp' )
				);
			}
		);
	}
}
