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
		if ( count( $this->errors ) ) {
			return true;
		}

		$fields = $this->form->get_form_object()->get_fields();
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

		$this->form->update_cache();

		return count( $this->errors ) > 0;
	}

	/**
	 * Render the validation.
	 *
	 * @return void
	 */
	public function render(): void {
		if ( ! $this->form->get_submission()->is_processed() ) {
			$block_id = $this->form->get_form_object()->get_acf_id();

			printf(
				'<input
				type="hidden"
				name="%s"
				value="%s"
			/>',
				esc_attr( Form::HIDDEN_FORM_ID ),
				esc_attr( $block_id )
			);

			wp_nonce_field( 'form_submission', Form::HIDDEN_FORM_ID . '_' . $block_id . '_nonce' );
		}

		if ( empty( $this->errors ) ) {
			return;
		}

		printf(
			'<div class="acf-form-blocks-validation">%s</div>',
			implode( '<br>', $this->errors )
		);
	}
}
