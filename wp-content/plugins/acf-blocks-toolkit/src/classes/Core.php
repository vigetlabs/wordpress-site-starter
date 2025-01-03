<?php
/**
 * Core API
 *
 * @package ACFBlocksToolkit
 */

namespace Viget\ACFBlocksToolkit;

/**
 * Core API
 */
class Core {

	/**
	 * Instance of this class.
	 *
	 * @var ?Core
	 */
	private static ?Core $instance = null;

	/**
	 * Block Icons
	 *
	 * @var ?BlockIcons
	 */
	public ?BlockIcons $block_icons = null;

	/**
	 * Breakpoint Visibility
	 *
	 * @var ?BreakpointVisibility
	 */
	public ?BreakpointVisibility $bp_visibility = null;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->block_icons   = new BlockIcons();
		$this->bp_visibility = new BreakpointVisibility();
	}

	/**
	 * Get the instance of this class.
	 *
	 * @return Core
	 */
	public static function instance(): Core {
		if ( null === self::$instance ) {
			self::$instance = new self();

			BlockRegistration::init();
			Settings::init();
		}

		return self::$instance;
	}
}
