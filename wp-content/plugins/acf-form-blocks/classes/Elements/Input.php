<?php
/**
 * Input Field Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Elements;

use ACFFormBlocks\Utilities\BlockTemplate\Block;
use ACFFormBlocks\Utilities\BlockTemplate\Template;
use Exception;

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

	/**
	 * Get the block template.
	 *
	 * @return array
	 * @throws Exception
	 */
	public function get_template(): array {
		return (
			new Template(
				new Block(
					'core/paragraph',
					[ 'placeholder' => __( 'Field Label...', 'acf-form-blocks' ) ]
				)
			)
		)->get();
	}
}
