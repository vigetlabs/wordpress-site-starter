<?php
/**
 * Form Meta Block Helper Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Blocks;

/**
 * Submit Block Class
 */
class FormMetaBlock extends Block {

	/**
	 * FormMetaBlock constructor.
	 *
	 * @param string|array $block_names The block name.
	 */
	public function __construct( string|array $block_names = 'acf/form-meta' ) {
		parent::__construct( $block_names );
	}

	/**
	 * Display the form meta value.
	 *
	 * @param string $block_content
	 *
	 * @return string
	 */
	public function render( string $block_content ): string {
		return 'THE META VALUE WILL SHOW UP HERE!';
	}
}
