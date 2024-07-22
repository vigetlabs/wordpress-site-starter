<?php
/**
 * Form Data Block Helper Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Blocks;

/**
 * Submit Block Class
 */
class FormDataBlock extends Block {

	/**
	 * FormDataBlock constructor.
	 *
	 * @param string|array $block_names The block name.
	 */
	public function __construct( string|array $block_names = 'acf/form-data' ) {
		parent::__construct( $block_names );
	}

	/**
	 * Display the form field value.
	 *
	 * @param string $block_content
	 *
	 * @return string
	 */
	public function render( string $block_content ): string {
		return 'THE FIELD VALUE WILL SHOW UP HERE!';
	}
}
