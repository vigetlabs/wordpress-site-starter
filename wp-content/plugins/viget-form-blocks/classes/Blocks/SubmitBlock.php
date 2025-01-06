<?php
/**
 * Submit Block Helper Class
 *
 * @package VigetFormBlocks
 */

namespace VigetFormBlocks\Blocks;

/**
 * Submit Block Class
 */
class SubmitBlock extends Block {

	/**
	 * SubmitBlock constructor.
	 *
	 * @param string|array $block_names The block name.
	 */
	public function __construct( string|array $block_names = 'acf/submit' ) {
		parent::__construct( $block_names );
	}

	/**
	 * Convert link tag to a button tag and add type submit
	 *
	 * @param string $block_content
	 *
	 * @return string
	 */
	public function render( string $block_content ): string {
		// convert link tag to a button tag and add type submit
		$block_content = str_replace( '<a', '<button type="submit"', $block_content );

		return str_replace( '</a>', '</button>', $block_content );
	}
}
