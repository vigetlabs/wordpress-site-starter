<?php
/**
 * Checkbox Class
 *
 * @package VigetFormBlocks
 */

namespace VigetFormBlocks\Elements;

use Exception;
use Viget\VigetBlocksToolkit\BlockTemplate\Block;
use Viget\VigetBlocksToolkit\BlockTemplate\Template;

/**
 * Class for Checkbox Fields
 */
class Checkbox extends Field {

	/**
	 * Required marker placement
	 *
	 * @var string
	 */
	protected string $req_marker_placement = 'before';

	/**
	 * Get the block template.
	 *
	 * @return array
	 * @throws Exception
	 */
	public function get_template(): array {
		return ( new Template( new Block( 'acf/label' ) ) )->get();
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
