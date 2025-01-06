<?php
/**
 * Form Validation
 *
 * @package VigetFormBlocks
 */

namespace VigetFormBlocks;

use VigetFormBlocks\Elements\Field;

/**
 * Submission Validation
 */
class Validation {

	/**
	 * The Form ID.
	 *
	 * @var string
	 */
	protected string $form_id;

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
		$this->form_id = $form->get_form_object()->get_id();
	}

	/**
	 * Get the form instance.
	 *
	 * @return ?Form
	 */
	public function get_form(): ?Form {
		return Form::find_form( $this->form_id );
	}

	/**
	 * Add an error.
	 *
	 * @param Field $field
	 * @param string $error_message
	 *
	 * @return void
	 */
	public function add_error( Field $field, string $error_message ): void {
		$this->errors[ $field->get_id() ] = $error_message;

		$this->get_form()->update_cache();
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

		$form = $this->get_form();

		if ( ! $form->get_submission()->has_submit() ) {
			return false;
		}

		$fields = $form->get_form_object()->get_fields();
		$data   = $form->get_submission()->get_data();

		foreach ( $fields as $field ) {
			if ( ! $field->is_required() ) {
				continue;
			}

			if ( ! empty( $data['content'][ $field->get_name() ]['value'] ) ) {
				continue;
			}

			$this->add_error( $field, $field->get_label() . ' ' . __( 'is required.', 'viget-form-blocks' ) );
		}

		return count( $this->errors ) > 0;
	}

	/**
	 * Render the validation.
	 *
	 * @return void
	 */
	public function render(): void {
		$form = $this->get_form();
		if ( ! $form->get_submission()->is_processed() ) {
			$block_id = $form->get_form_object()->get_id();

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
			'<div class="viget-form-blocks-validation">%s</div>',
			implode( '<br>', $this->errors )
		);
	}
}
