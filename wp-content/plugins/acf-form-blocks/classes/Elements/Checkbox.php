<?php
/**
 * Checkbox Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Elements;

use Exception;
use Viget\ACFBlocksToolkit\BlockTemplate\Block;
use Viget\ACFBlocksToolkit\BlockTemplate\Template;

/**
 * Class for Checkbox Fields
 */
class Checkbox extends Field {

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
					[ 'placeholder' => __( 'Label...', 'acf-form-blocks' ) ]
				)
			)
		)->get();
	}

	/**
	 * Get the value attribute of the checkbox.
	 *
	 * @return string
	 */
	public function get_value_attr(): string {
		$custom_value = $this->get_field_data( 'checkbox_value', false );
		$value        = $this->get_field_data( 'value', 1 );

		if ( ! $custom_value ) {
			$value = $this->get_label();
		}

		return $value;
	}

	/**
	 * Whether the checkbox should be checked or not.
	 *
	 * @return bool
	 */
	public function is_checked(): bool {
		if ( ! $this->get_fieldset() ) {
			return $this->get_value_attr() === parent::get_value();
		}

		$values = $this->get_fieldset()->get_value() ?: [];

		if ( in_array( $this->get_value(), $values, true ) ) {
			return true;
		}

		return false;
	}
}
