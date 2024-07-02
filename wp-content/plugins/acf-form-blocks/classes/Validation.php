<?php
/**
 * Form Validation
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks;

/**
 * Submission Validation
 */
class Validation {

	/**
	 * The Form.
	 *
	 * @var Form
	 */
	protected Form $form;

	/**
	 * Errors.
	 *
	 * @var array
	 */
	protected array $errors = [];

	/**
	 * Constructor.
	 */
	public function __construct( Form $form ) {
		$this->form = $form;
	}

	/**
	 * Check if the form has errors.
	 *
	 * @return bool
	 */
	public function has_errors(): bool {
		$fields = $this->form->get_fields();
		$data   = $this->form->get_submission()->get_data();

		foreach ( $fields as $field ) {
			if ( ! $field->is_required() ) {
				continue;
			}

			if ( ! empty( $data[ $field->get_name() ] ) ) {
				continue;
			}

			$this->errors[] = $field->get_label() . ' ' . __( 'is required.', 'acf-form-blocks' );
		}

		return count( $this->errors ) > 0;
	}

	/**
	 * Render the validation.
	 *
	 * @return void
	 */
	public function render(): void {
		if ( empty( $this->errors ) ) {
			return;
		}

		printf(
			'<div class="acf-form-blocks-validation">%s</div>',
			implode( '<br>', $this->errors )
		);
	}
}
