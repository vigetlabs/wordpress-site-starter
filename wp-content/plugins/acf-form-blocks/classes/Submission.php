<?php
/**
 * Form Submission
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks;

use ACFFormBlocks\Elements\Field;

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
	 * The form data.
	 *
	 * @var array
	 */
	private array $data = [];

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
			$nonce_field = Form::HIDDEN_FORM_ID . '_' . get_block_id( $this->form->get_form_element() ) . '_nonce';

			if ( empty( $_REQUEST[ $nonce_field ] ) || ! wp_verify_nonce( $_REQUEST[ $nonce_field ], 'form_submission' ) ) {
				return false;
			}

			$this->nonce_verified = true;
			$this->form->update_cache();
		}

		return ! empty( $_REQUEST[ Form::HIDDEN_FORM_ID ] ) && get_block_id( $this->form->get_form_element() ) === $_REQUEST[ Form::HIDDEN_FORM_ID ];
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
		if ( ! empty( $this->data ) ) {
			return $this->data;
		}

		$fields = $this->form->get_form_object()->get_fields();

		foreach ( $fields as $field ) {
			$value = [
				'label' => $field->get_label(),
			];

			if ( 'input' === $field->get_block_name() && 'file' === $field->get_type() ) {
				$value['value'] = $this->handle_upload( $field );
			} else {
				$value['value'] = $_REQUEST[ $field->get_name() ] ?? null;
			}

			$this->data[ $field->get_name() ] = $value;
		}

		$this->form->update_cache();

		return $this->data;
	}

	/**
	 * Handle the file upload.
	 *
	 * @param Field $field The Field.
	 *
	 * @return ?string
	 */
	private function handle_upload( Field $field ): ?string {
		$upload = $_FILES[ $field->get_name() ] ?? null;

		if ( ! $upload ) {
			return null;
		}

		$upload_dir    = wp_upload_dir();
		$upload_folder = $upload_dir['basedir'] . '/form-submissions';

		// Make sure root directory is protected.
		if ( ! is_dir( $upload_folder ) ) {
			wp_mkdir_p( $upload_folder );
			file_put_contents( $upload_folder . '/index.php', "<?php // Silence is golden.\n" );
		}

		$upload_folder .= '/' . $this->form->get_form_id();

		// Make sure upload directory is protected.
		if ( ! is_dir( $upload_folder ) ) {
			wp_mkdir_p( $upload_folder );
			file_put_contents( $upload_folder . '/index.php', "<?php // Silence is golden.\n" );
		}

		$upload_name = wp_unique_filename( $upload_folder, $upload['name'] );
		$upload_path = $upload_folder . '/' . $upload_name;

		if ( ! move_uploaded_file( $upload['tmp_name'], $upload_path ) ) {
			return null;
		}

		return $upload_path;
	}

	/**
	 * Save the form data.
	 *
	 * @return void
	 */
	protected function save(): void {
		$form_name = $this->form->get_form_object()->get_name();

		wp_insert_post( [
			'post_type'    => ACFFB_SUBMISSION_POST_TYPE,
			'post_title'   => __( 'Submission from', 'acf-form-blocks' ) . ' ' . $form_name,
			'post_status'  => 'publish',
			'post_content' => json_encode( $this->get_data() ),
		] );
	}
}
