<?php
/**
 * Child Fields Trait
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Traits;

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Form;

/**
 * Trait for Child Fields
 */
trait ChildFields {

	/**
	 * Get the child fields.
	 *
	 * @param Form|null $form The form object.
	 *
	 * @return Field[]
	 */
	public function get_child_fields( ?Form $form = null ): array {
		if ( ! $this->get_form() && $form ) {
			$this->form = $form;
		}

		$fields   = $this->form->get_form_object()->get_all_fields();
		$children = [];

		foreach ( $fields as $field ) {
			if ( ! $field->get_parent_id() ) {
				continue;
			}

			if ( $this->get_id() === $field->get_parent_id() ) {
				$children[ $field->get_id() ] = $field;
			}
		}

		return $children;
	}
}
