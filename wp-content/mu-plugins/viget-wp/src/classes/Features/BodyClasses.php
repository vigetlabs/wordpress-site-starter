<?php
/**
 * Body Classes
 *
 * @package VigetWP
 */

namespace VigetWP\Features;

/**
 * Body Classes
 */
class BodyClasses {

	/**
	 * BodyClasses constructor.
	 */
	public function __construct() {
		// Page Slug.
		$this->set_page_slug_body_class();
	}

	/**
	 * Set the page slug as a body class.
	 *
	 * @return void
	 */
	private function set_page_slug_body_class(): void {
		add_filter(
			'body_class',
			function ( array $classes ): array {
				if ( is_page() ) {
					$page = get_queried_object();
					$classes[] = 'pageslug-' . $page->post_name;
				}
				return $classes;
			}
		);
	}
}
