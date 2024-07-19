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
	protected array $data = [];

	/**
	 * Is the submission processed.
	 *
	 * @var bool
	 */
	protected bool $is_processed = false;

	/**
	 * Is the nonce verified?
	 *
	 * @var bool
	 */
	protected bool $nonce_verified = false;

	/**
	 * The submission ID.
	 *
	 * @var ?int
	 */
	protected ?int $submission_id = null;

	/**
	 * Constructor.
	 *
	 * @param Form $form The Form.
	 */
	public function __construct( Form $form ) {
		$this->form = $form;

		// Disable caching.
		$this->submission_nocache_headers();

		// Save the form data
		$this->save_submission();
	}

	/**
	 * Check if the form has been submitted.
	 *
	 * @return bool
	 */
	public function has_submit(): bool {
		if ( ! $this->nonce_verified ) {
			$nonce_field = Form::HIDDEN_FORM_ID . '_' .$this->form->get_form_object()->get_id() . '_nonce';

			if ( empty( $_REQUEST[ $nonce_field ] ) || ! wp_verify_nonce( $_REQUEST[ $nonce_field ], 'form_submission' ) ) {
				return false;
			}

			$this->nonce_verified = true;
			$this->form->update_cache();
		}

		return ! empty( $_REQUEST[ Form::HIDDEN_FORM_ID ] ) && $this->form->get_form_object()->get_id() === $_REQUEST[ Form::HIDDEN_FORM_ID ];
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
	 * Prepare for submission.
	 *
	 * @return void
	 */
	private function prepare_submission(): void {
		// Disable caching.
		$this->set_nocache_headers();

		// Start Session
		$this->start_session();
	}

	/**
	 * Start the session.
	 *
	 * @return void
	 */
	private function start_session(): void {
		if ( headers_sent() ) {
			return;
		}

		if ( ! session_id() ) {
			session_start();
		}
	}

	/**
	 * Process the form submission.
	 *
	 * @return void
	 */
	public function process(): void {
		$this->prepare_submission();

		if ( $this->is_processed() ) {
			return;
		}

		if ( ! $this->has_submit() ) {
			return;
		}

		if ( $this->form->get_validation()->has_errors() ) {
			return;
		}

		$this->is_processed = true;
		$this->form->get_notification();
		$this->form->get_confirmation();
		$this->form->update_cache();

		do_action( 'acffb_process_submission', $this );
	}

	/**
	 * Hook during the submission to no cache headers.
	 *
	 * @return void
	 */
	public function submission_nocache_headers(): void {
		add_action(
			'acffb_process_submission',
			function () {
				$this->set_nocache_headers();
			},
			2
		);
	}

	/**
	 * Set the no cache headers.
	 *
	 * @return void
	 */
	private function set_nocache_headers(): void {
		if ( headers_sent() ) {
			return;
		}

		header( 'Cache-Control: no-cache, no-store, must-revalidate, max-age=0' );
		header( 'Cache-Control: post-check=0, pre-check=0', false );
		header( 'Pragma: no-cache' );
	}

	/**
	 * Get the field data.
	 *
	 * @param string $field_name The Field Name.
	 *
	 * @return mixed
	 */
	public function get_field_data( string $field_name ): mixed {
		$data = $this->get_data();
		return $data['content'][ $field_name ]['value'] ?? null;
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

		$fields     = $this->form->get_form_object()->get_fields();
		$this->data = [
			'content' => [],
			'meta'    => [
				'_url'           => esc_url( $_SERVER['REQUEST_URI'] ),
				'_ip'            => sanitize_text_field( $_SERVER['REMOTE_ADDR'] ),
				'_agent'         => sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ),
				'_method'        => sanitize_text_field( $_SERVER['REQUEST_METHOD'] ),
				'_form_id'       => $this->form->get_form_object()->get_id(),
				'_form_markup'   => $this->form->get_form_object()->get_form_markup(),
				'_form_context'  => $this->form->get_form_object()->get_form_context(),
				'_form_name'     => $this->form->get_form_object()->get_name(),
				'_post_id'       => get_queried_object_id(),
				'_confirmation'  => [
					'type' => $this->form->get_confirmation()->get_type(),
					'page' => $this->form->get_form_object()->get_form_data( 'page' ),
					'url'  => $this->form->get_form_object()->get_form_data( 'custom_url' ),
				],
				'_notifications' => [
					'admin'        => $this->form->get_notification()->is_admin_email_enabled(),
					'confirmation' => $this->form->get_notification()->is_confirmation_email_enabled(),
					'custom'       => $this->form->get_notification()->is_custom_email_enabled(),
					'recipient'    => $this->form->get_notification()->get_custom_email(),
				],
			],
		];

		foreach ( $fields as $field ) {
			if ( ! method_exists( $field, 'get_child_fields' ) ) {
				continue;
			}

			$children = $field->get_child_fields( $this->form );

			foreach ( $children as $child ) {
				if ( array_key_exists( $child->get_id(), $fields ) ) {
					unset( $fields[ $child->get_id() ] );
				}
			}
		}

		foreach ( $fields as $field ) {
			$data = [
				'label' => $field->get_field_label(),
				'value' => $field->sanitize_input( null, $this->form ),
			];

			$this->data['content'][ $field->get_name() ] = $data;
		}

		$this->data = apply_filters( 'acffb_submission_data', $this->data );

		$this->form->update_cache();

		return $this->data;
	}

	/**
	 * Save the form data.
	 *
	 * @return void
	 */
	public function save_submission(): void {
		add_action(
			'acffb_process_submission',
			function () {
				if ( ! $this->form->get_form_object()->save_data_enabled() ) {
					return;
				}

				$form_name      = $this->form->get_form_object()->get_name();
				$form_data      = $this->get_data();
				$submission_key = md5( serialize( $form_data['content'] ) );
				$form_post      = apply_filters(
					'acffb_submission_post',
					[
						'post_type'    => Admin\Submission::POST_TYPE,
						'post_title'   => $form_name  . ' ' . __( 'Submission', 'acf-form-blocks' ),
						'post_status'  => 'publish',
						'post_name'    => sanitize_title( $form_name . ' ' . $submission_key ),
						'post_content' => wp_json_encode(  $form_data['content'] ),
					]
				);

				$submission_id = wp_insert_post( $form_post );

				if ( ! $submission_id ) {
					return;
				}

				$this->submission_id = $submission_id;

				foreach ( $form_data['meta'] as $meta_key => $meta_value ) {
					update_post_meta( $submission_id, $meta_key, $meta_value );
				}

				$this->form->update_cache();
			},
			5
		);
	}

	/**
	 * Get the submission admin URL.
	 *
	 * @return ?string
	 */
	public function get_submission_url(): ?string {
		if ( ! $this->submission_id ) {
			return null;
		}

		return get_edit_post_link( $this->submission_id );
	}
}
