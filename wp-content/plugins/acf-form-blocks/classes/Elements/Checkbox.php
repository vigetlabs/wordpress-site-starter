<?php
/**
 * Checkbox Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Elements;

use ACFFormBlocks\Traits\Fieldset;
use ACFFormBlocks\Utilities\BlockTemplate\Block;
use ACFFormBlocks\Utilities\BlockTemplate\Template;
use Exception;

/**
 * Class for Checkbox Fields
 */
class Checkbox extends Field {
	use Fieldset;

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
	 * Get the name attribute.
	 *
	 * @return string
	 */
	public function get_name_attr(): string {
		if ( $this->get_fieldset() ) {
			return $this->get_fieldset()->get_name() . '[]';
		}

		return parent::get_name();
	}

	/**
	 * Get the value attribute of the checkbox.
	 *
	 * @return string
	 */
	public function get_value(): string {
		$custom_value = $this->get_field_data( 'checkbox_value', false );
		$value        = $this->get_field_data( 'value', 1 );

		if ( ! $custom_value ) {
			$checkbox = $this->get_form()?->get_form_object()->get_field_by_id( $this->get_id() );
			if ( $checkbox ) {
				$checkbox = Field::factory( $checkbox->get_block(), $this->context );
				$value    = $checkbox->get_label();
			}
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
			return $this->get_value() === parent::get_value();
		}

		if ( in_array( $this->get_value(), $this->get_fieldset()->get_value(), true ) ) {
			return true;
		}

		return false;
	}
}
