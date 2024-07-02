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
	 * Is the submission processed.
	 *
	 * @var bool
	 */
	private bool $is_processed = false;

	/**
	 * Is the nonce verified?
	 *
	 * @var bool
	 */
	private bool $nonce_verified = false;

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
		if ( ! $this->nonce_verified ) {
			$nonce_field = Form::HIDDEN_FORM_ID . '_' . get_block_id( $this->form->get_form() ) . '_nonce';

			if ( empty( $_REQUEST[ $nonce_field ] ) || ! wp_verify_nonce( $_REQUEST[ $nonce_field ], 'form_submission' ) ) {
				return false;
			}

			$this->nonce_verified = true;
			$this->form->update_cache();
		}

		return ! empty( $_REQUEST[ Form::HIDDEN_FORM_ID ] ) && get_block_id( $this->form->get_form() ) === $_REQUEST[ Form::HIDDEN_FORM_ID ];
	}

	/**
	 * Check if the form submission was successful.
	 *
	 * @return bool
	 */
	public function is_processed(): bool {
		return $this->is_processed;
	}

	/**
	 * Process the form submission.
	 *
	 * @return void
	 */
	public function process(): void {
		if ( $this->is_processed() ) {
			return;
		}

		if ( ! $this->has_submit() ) {
			return;
		}

		if ( $this->form->get_validation()->has_errors() ) {
			return;
		}

		$this->save();
		$this->is_processed = true;
		$this->form->update_cache();

		if ( 'redirect' !== $this->form->get_confirmation()->get_type() ) {
			return;
		}

		wp_safe_redirect( $this->form->get_confirmation()->get_redirect() );
		exit;
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

	/**
	 * Save the form data.
	 *
	 * @return void
	 */
	protected function save(): void {
		wp_insert_post( [
			'post_type'    => ACFFB_SUBMISSION_POST_TYPE,
			'post_title'   => __( 'Submission from', 'acf-form-blocks' ) . ' ' . get_the_title(),
			'post_status'  => 'publish',
			'post_content' => json_encode( $this->get_data() ),
		] );
	}
}
