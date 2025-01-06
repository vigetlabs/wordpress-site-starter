<?php
/**
 * Legend Class
 *
 * @package VigetFormBlocks
 */

namespace VigetFormBlocks\Elements;

use Viget\VigetBlocksToolkit\BlockTemplate\Block;
use Viget\VigetBlocksToolkit\BlockTemplate\Template;

/**
 * Class for Legend Fields
 */
class Legend extends Field {

	/**
	 * Get the block attributes.
	 *
	 * @return array
	 */
	public function get_attrs(): array {
		$attrs = parent::get_attrs();

		unset( $attrs['name'] );
		$attrs['data-supports-jsx'] = null;

		return $attrs;
	}

	/**
	 * Get the block template.
	 *
	 * @return array
	 */
	public function get_template(): array {
		return ( new Template( ( new Block( 'core/paragraph', [ 'placeholder' => __( 'Legend...', 'acf-field-blocks' ), 'lock' => 'all' ] ) ) ) )->get();
	}
}
