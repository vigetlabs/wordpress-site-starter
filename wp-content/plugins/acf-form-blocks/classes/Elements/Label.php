<?php
/**
 * Label Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Elements;

use Viget\ACFBlocksToolkit\BlockTemplate\Block;
use Viget\ACFBlocksToolkit\BlockTemplate\Template;

/**
 * Class for Label Fields
 */
class Label extends Field {

	/**
	 * Get the block attributes.
	 *
	 * @return array
	 */
	public function get_attrs(): array {
		$attrs = parent::get_attrs();

		unset( $attrs['name'] );
		$attrs['data-supports-jsx'] = null;

		$parent = $this->get_parent_field();
		if ( $parent && ! is_admin() ) {
			$attrs['for'] = $parent->get_id_attr();
		}

		return $attrs;
	}

	/**
	 * Get the block template.
	 *
	 * @return array
	 */
	public function get_template(): array {
		return ( new Template( ( new Block( 'core/paragraph', [ 'placeholder' => __( 'Field Label...', 'acf-field-blocks' ) ] ) ) ) )->get();
	}
}
