<?php
/**
 * Form Submission
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks;

/**
 * Submission Confirmation
 */
class Submission {

	/**
	 * The Form.
	 *
	 * @var Form
	 */
	protected Form $form;

	/**
	 * Constructor.
	 *
	 * @param Form $form The Form.
	 */
	public function __construct( Form $form ) {
		$this->form = $form;
	}

	/**
	 * Check if the form has been submitted.
	 *
	 * @return bool
	 */
	public function has_submit(): bool {
		return ! empty( $_REQUEST[ Form::HIDDEN_FORM_ID ] ) && get_block_id( $this->form->get_form() ) === $_REQUEST[ Form::HIDDEN_FORM_ID ];
	}

	/**
	 * Check if the form submission was successful.
	 *
	 * @return bool
	 */
	public function is_success(): bool {
		if ( ! $this->has_submit() ) {
			return false;
		}

		if ( $this->form->get_validation()->has_errors() ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the form data.
	 *
	 * @return array
	 */
	public function get_data(): array {
		$fields = $this->form->get_fields();
		$data   = [];

		foreach ( $fields as $field ) {
			$data[ $field->get_name() ] = $_REQUEST[ $field->get_name() ] ?? null;
		}

		return $data;
	}
}
