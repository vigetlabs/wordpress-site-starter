<?php
/**
 * Field Block Helper Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Blocks;

/**
 * Class for Field Block Actions
 */
class FieldBlock extends Block {

	/**
	 * Set the block attributes.
	 *
	 * @param array $attrs The block attributes.
	 *
	 * @return array
	 */
	public function set_attrs( array $attrs ): array {
		unset( $attrs['data-supports-jsx'] ); // Unnecessary for input fields.

		$attrs['name'] = $this->field->get_name_attr();
		$attrs['id']   = $this->field->get_id_attr();

		if ( $this->field->get_placeholder() ) {
			$attrs['placeholder'] = $this->field->get_placeholder();
		}

		if ( $this->field->is_required() ) {
			$attrs['required'] = true;
		}

		$logic = $this->field->get_conditional_logic();
		if ( ! is_admin() && $logic ) {
			$attrs['data-conditional-rules'] = wp_json_encode( $logic );
		}

		return $attrs;
	}
}
