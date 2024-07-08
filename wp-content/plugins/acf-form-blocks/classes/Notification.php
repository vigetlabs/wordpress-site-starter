<?php
/**
 * Form Notification
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks;

/**
 * Form Confirmation
 */
class Notification {

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
	 * Process the notification emails
	 *
	 * @return void
	 */
	public function process(): void {
		if ( $this->is_admin_email_enabled() ) {
			$this->send_admin_email();
		}

		if ( $this->is_confirmation_email_enabled() ) {
			$this->send_confirmation_email();
		}

		if ( $this->is_custom_email_enabled() ) {
			$this->send_custom_email();
		}
	}

	/**
	 * Send the Admin Email
	 *
	 * @return void
	 */
	private function send_admin_email(): void {
		$submission_url = $this->form->get_submission()->get_submission_url();

		$form_name = $this->form->get_form_object()->get_name();
		$subject   = __( 'New Form Submission', 'acf-form-blocks' ) . ': ' . $form_name;
		$body      = __( 'A new form submission has been received for the form', 'acf-blocks-toolkit' ) . ': ' . $form_name . '<br><br>';
		$body     .= wp_json_encode( $this->form->get_submission()->get_data(), JSON_PRETTY_PRINT );

		if ( $submission_url ) {
			$body .= '<br><br>';
			$body .= sprintf(
				'<a href="%s">%s</a>',
				esc_url( $submission_url ),
				__( 'Click here to view the submission details.', 'acf-blocks-toolkit' )
			);
		}

		$recipient = get_option( 'admin_email' );

		$this->send_email( $recipient, $subject, $body );
	}

	/**
	 * Send the Confirmation Email
	 *
	 * @return void
	 */
	private function send_confirmation_email(): void {
		$form_name = $this->form->get_form_object()->get_name();
		$subject   = __( 'Form Submission Confirmation', 'acf-form-blocks' );
		$body      = __( 'This is a confirmation that your submission has been received for the form', 'acf-blocks-toolkit' ) . ': ' . $form_name . '<br><br>';
		$body     .= wp_json_encode( $this->form->get_submission()->get_data(), JSON_PRETTY_PRINT );

		$recipient = $this->get_confirmation_recipient();

		$this->send_email( $recipient, $subject, $body );
	}

	/**
	 * Get the Confirmation Email address
	 *
	 * @return string
	 */
	private function get_confirmation_recipient(): string {
		foreach ( $this->form->get_form_object()->get_fields() as $field ) {
			if ( 'input' === $field->get_block_name() && 'email' === $field->get_type() ) {
				return $field->get_value();
			}
		}

		return '';
	}

	/**
	 * Send the Custom Email
	 *
	 * @return void
	 */
	private function send_custom_email(): void {
		$form_name = $this->form->get_form_object()->get_name();
		$subject   = __( 'New Form Submission', 'acf-form-blocks' ) . ': ' . $form_name;
		$body      = __( 'A new form submission has been received for the form', 'acf-blocks-toolkit' ) . ': ' . $form_name . '<br><br>';
		$body     .= wp_json_encode( $this->form->get_submission()->get_data(), JSON_PRETTY_PRINT );

		$recipient = $this->get_custom_email();

		$this->send_email( $recipient, $subject, $body );
	}

	/**
	 * Check if Admin Email Notification is enabled.
	 *
	 * @return bool
	 */
	public function is_admin_email_enabled(): bool {
		return boolval( $this->form->get_form_object()->get_form_data( 'admin_email' ) );
	}

	/**
	 * Check if Confirmation Email Notification is enabled.
	 *
	 * @return bool
	 */
	public function is_confirmation_email_enabled(): bool {
		return boolval( $this->form->get_form_object()->get_form_data( 'confirmation_email' ) );
	}

	/**
	 * Check if Custom Email Notification is enabled.
	 *
	 * @return bool
	 */
	public function is_custom_email_enabled(): bool {
		return boolval( $this->form->get_form_object()->get_form_data( 'custom_email' ) );
	}

	/**
	 * Get the Custom Email address.
	 *
	 * @return string
	 */
	public function get_custom_email(): string {
		return (string) $this->form->get_form_object()->get_form_data( 'email_recipient' );
	}

	/**
	 * Send an email.
	 *
	 * @param string $recipient
	 * @param string $subject
	 * @param string $body
	 * @param array  $headers
	 * @param array  $attachments
	 *
	 * @return void
	 */
	public function send_email( string $recipient, string $subject, string $body, array $headers = [], array $attachments = [] ): void {
		wp_mail(
			$recipient,
			$subject,
			$body,
			$headers,
			$attachments
		);
	}
}
