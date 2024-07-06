<?php
/**
 * Input Field Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Elements;

/**
 * Class for Input Fields
 */
class Input extends Field {

	/**
	 * Get the field input type.
	 *
	 * @return string
	 */
	public function get_input_type(): string {
		return $this->get_field_data( 'type', 'text' );
	}

	/**
	 * Get the max length.
	 *
	 * @return ?int
	 */
	public function get_maxlength(): ?int {
		return $this->get_field_data( 'maxlength' ) ? intval( $this->get_field_data( 'maxlength' ) ) : null;
	}
}
