<?php
/**
 * All Fields Block Helper Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Blocks;

/**
 * Submit Block Class
 */
class AllFieldsBlock extends Block {

	/**
	 * AllFieldsBlock constructor.
	 *
	 * @param string|array $block_names The block name.
	 */
	public function __construct( string|array $block_names = 'acf/all-fields' ) {
		parent::__construct( $block_names );
	}

	/**
	 * Display all the form fields.
	 *
	 * @param string $block_content
	 *
	 * @return string
	 */
	public function render( string $block_content ): string {
		return 'ALL FIELDS SHOW UP HERE!';
	}
}
