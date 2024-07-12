<?php
/**
 * Fieldset Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Elements;

use Exception;

/**
 * Class for Radios Fields
 */
class Fieldset extends Field {

	/**
	 * Get the block template.
	 *
	 * @return array
	 * @throws Exception
	 */
	public function get_value(): array {
		if ( empty( $this->context['is_checkbox_group'] ) ) {
			return [ parent::get_value() ];
		}

		return ! empty( $_REQUEST[ $this->get_name() ] ) ? $_REQUEST[ $this->get_name() ] : [];
	}
}
