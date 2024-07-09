<?php
/**
 * Fieldset Block Helper Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Blocks;

/**
 * Fieldset Block Class
 */
class FieldsetBlock extends FieldBlock {

	/**
	 * FieldsetBlock constructor.
	 *
	 * @param string|array $block_names The block name.
	 */
	public function __construct( string|array $block_names = 'acf/fieldset' ) {
		parent::__construct( $block_names );
	}

}
