<?php
/**
 * Legend Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Elements;

use ACFFormBlocks\Form;
use ACFFormBlocks\Utilities\Cache;

/**
 * Class for Fields
 */
class Legend extends Field {

	/**
	 * Get the fieldset.
	 *
	 * @return ?Field
	 */
	public function get_fieldset(): ?Field {
		if ( empty( $this->context['acffb/fieldset_id'] ) || empty( $this->context['acffb/form_id'] ) ) {
			return null;
		}

		$form = Cache::get( $this->context['acffb/form_id'] );

		if ( ! $form ) {
			$form = acffb_get_form();
		}

		return $form?->get_form_object()->get_field_by_id( $this->context['acffb/fieldset_id'] );
	}
}
