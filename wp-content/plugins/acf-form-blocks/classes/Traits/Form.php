<?php
/**
 * Form Trait
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Traits;

use ACFFormBlocks\Form as FormObject;
use ACFFormBlocks\Utilities\Cache;

/**
 * Trait for retrieve parent fieldset.
 */
trait Form {

	/**
	 * Form object.
	 *
	 * @var ?FormObject
	 */
	public ?FormObject $form = null;

	/**
	 * Get the form object.
	 *
	 * @return ?FormObject
	 */
	public function get_form(): ?FormObject {
		if ( ! $this->form ) {
			if ( ! empty( $this->context['acffb/form_id'] ) ) {
				$this->form = Cache::get($this->context['acffb/form_id']);
			}

			if ( ! $this->form ) {
				$this->form = acffb_get_form();
			}
		}

		return $this->form;
	}
}
