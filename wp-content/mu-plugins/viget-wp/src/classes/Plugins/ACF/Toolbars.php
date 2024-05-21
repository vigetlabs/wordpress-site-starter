<?php
/**
 * @package VigetWP
 */

namespace VigetWP\Plugins\ACF;

/**
 * Toolbars Class
 */
class Toolbars {

	/**
	 * The key for the toolbar
	 */
	const TOOLBAR_KEY = 'Vigebasic';

	/**
	 * Constructor
	 */
	public function __construct() {
		// Add the Viget Toolbars
		$this->add_viget_toolbars();
	}

	/**
	 * Add the Viget Toolbars
	 *
	 * @return void
	 */
	private function add_viget_toolbars(): void {
		add_filter(
			'acf/fields/wysiwyg/toolbars',
			function ( array $toolbars ): array {
				if ( ! empty( $toolbars[ self::TOOLBAR_KEY ] ) ) {
					return $toolbars;
				}

				$toolbars[ self::TOOLBAR_KEY ] = [
					1 => [
						'formatselect',
						'styleselect',
						'bold',
						'italic',
						'link',
						'bullist',
						'numlist'
					],
				];

				return $toolbars;
			}
		);
	}
}
