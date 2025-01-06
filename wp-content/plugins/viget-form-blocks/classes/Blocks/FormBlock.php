<?php
/**
 * Form Block Helper Class
 *
 * @package VigetFormBlocks
 */

namespace VigetFormBlocks\Blocks;

/**
 * Form Block
 */
class FormBlock extends Block {

	/**
	 * RadiosBlock constructor.
	 *
	 * @param string|array $block_names The block name.
	 */
	public function __construct( string|array $block_names = 'acf/form' ) {
		parent::__construct( $block_names );
	}

	/**
	 * Perform Template Redirect Actions
	 *
	 * @return void
	 */
	public function do_template_redirect(): void {
		// Handle form submission and redirects.
		$this->form->get_submission()->process();
	}
}
