<?php
/**
 * Submit button
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Blocks;

/**
 * Submit Block Class
 */
class SubmitBlock extends FieldBlock {

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

// Init block actions and filters.
( new SubmitBlock( 'acf/submit' ) );
