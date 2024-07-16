<?php
/**
 * Legend Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Elements;

use ACFFormBlocks\Utilities\BlockTemplate\Block;
use ACFFormBlocks\Utilities\BlockTemplate\Template;

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

	public function get_template(): array {
		return ( new Template( ( new Block( 'core/paragraph', [ 'placeholder' => __( 'Legend...', 'acf-field-blocks' ), 'lock' => 'all' ] ) ) ) )->get();
	}
}
