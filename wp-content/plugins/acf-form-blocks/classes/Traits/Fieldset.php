<?php
/**
 * Fieldset Trait
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Traits;

use ACFFormBlocks\Elements\Field;

/**
 * Trait for retrieve parent fieldset.
 */
trait Fieldset {
	use Form;

	/**
	 * Get the fieldset.
	 *
	 * @return ?Field
	 */
	public function get_fieldset(): ?Field {
		if ( empty( $this->context['acffb/fieldset_id'] ) ) {
			return null;
		}

		$fieldset = $this->get_form()?->get_form_object()->get_field_by_id( $this->context['acffb/fieldset_id'] );

		if ( $fieldset ) {
			$fieldset = Field::factory( $fieldset->get_block(), $this->context );
		}

		return $fieldset;
	}

}
