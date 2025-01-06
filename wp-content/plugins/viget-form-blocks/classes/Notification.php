<?php
/**
 * Form Notification
 *
 * @package VigetFormBlocks
 */

namespace VigetFormBlocks;

use VigetFormBlocks\Admin\EmailTemplate;

/**
 * Form Confirmation
 */
class Notification {

	/**
	 * The Form ID.
	 *
	 * @var string
	 */
	protected string $form_id;

	/**
	 * Constructor.
	 *
	 * @param Form $form The Form.
	 */
	public function __construct( Form $form ) {
		$this->form_id = $form->get_form_object()->get_id();

		// Add the hooks.
		$this->add_hooks();
	}

	private function add_hooks(): void {
		// Process notifications on form submission.
		add_action( 'vgtfb_process_submission', [ $this, 'process' ], 10 );
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
		$form           = $this->get_form();
		$submission_url = $form->get_submission()->get_submission_url();

		$form_name = $form->get_form_object()->get_name();
		$recipient = get_option( 'admin_email' );
		$subject   = __( 'New Form Submission', 'viget-form-blocks' ) . ': ' . $form_name;
		$body      = __( 'A new form submission has been received for the form', 'viget-form-blocks' ) . ': ' . $form_name . '<br><br>';
		$body     .= wp_json_encode( $form->get_submission()->get_data(), JSON_PRETTY_PRINT );

		if ( $submission_url ) {
			$body .= '<br><br>';
			$body .= sprintf(
				'<a href="%s">%s</a>',
				esc_url( $submission_url ),
				__( 'Click here to view the submission details.', 'viget-form-blocks' )
			);
		}

		$template_id = $this->get_admin_template();

		if ( $template_id ) {
			$template = get_post( $template_id );
			$subject  = get_field( 'email_subject', $template_id ) ?: $subject;
			$body     = apply_filters( 'the_content', $template->post_content ) ?: $body;
		}

		$this->send_email( $recipient, $subject, $body );
	}

	/**
	 * Send the Confirmation Email
	 *
	 * @return void
	 */
	private function send_confirmation_email(): void {
		$recipient = $this->get_confirmation_recipient();

		if ( ! $recipient ) {
			return;
		}

		$form      = $this->get_form();
		$form_name = $form->get_form_object()->get_name();
		$subject   = __( 'Form Submission Confirmation', 'viget-form-blocks' );
		$body      = __( 'This is a confirmation that your submission has been received for the form', 'viget-form-blocks' ) . ': ' . $form_name . '<br><br>';
		$body     .= wp_json_encode( $form->get_submission()->get_data(), JSON_PRETTY_PRINT );

		$template_id = $this->get_confirmation_template();

		if ( $template_id ) {
			$template = get_post( $template_id );
			$subject  = get_field( 'email_subject', $template_id ) ?: $subject;
			$body     = apply_filters( 'the_content', $template->post_content ) ?: $body;
		}

		$this->send_email( $recipient, $subject, $body );
	}

	/**
	 * Get the Confirmation Email address
	 *
	 * @return string
	 */
	private function get_confirmation_recipient(): string {
		$emails = $this->get_form()->get_form_object()->get_fields_by_type( 'input', 'email' );

		if ( ! $emails ) {
			return '';
		}

		$recipients = '';

		foreach ( $emails as $email ) {
			$recipient = $email->get_value_attr();
			if ( $recipient ) {
				$recipients .= $recipients ? ',' . $recipient : $recipient;
			}
		}

		return $recipients;
	}

	/**
	 * Send the Custom Email
	 *
	 * @return void
	 */
	private function send_custom_email(): void {
		$form      = $this->get_form();
		$form_name = $form->get_form_object()->get_name();
		$recipient = $this->get_custom_email();
		$subject   = __( 'New Form Submission', 'viget-form-blocks' ) . ': ' . $form_name;
		$body      = __( 'A new form submission has been received for the form', 'viget-form-blocks' ) . ': ' . $form_name . '<br><br>';
		$body     .= wp_json_encode( $form->get_submission()->get_data(), JSON_PRETTY_PRINT );

		$template_id = $this->get_custom_template();

		if ( $template_id ) {
			$template = get_post( $template_id );
			$subject  = get_field( 'email_subject', $template_id ) ?: $subject;
			$body     = apply_filters( 'the_content', $template->post_content ) ?: $body;
		}

		$this->send_email( $recipient, $subject, $body );
	}

	/**
	 * Wrap Email body in Header and Footer.
	 *
	 * @param string $body
	 *
	 * @return string
	 */
	private function wrap_body( string $body ): string {
		$header = Views::get( 'parts/email-header' );
		$footer = Views::get( 'parts/email-footer' );

		return $header . $body . $footer;
	}

	/**
	 * Check if Admin Email Notification is enabled.
	 *
	 * @return bool
	 */
	public function is_admin_email_enabled(): bool {
		return boolval( $this->get_form()->get_form_object()->get_form_data( 'admin_email' ) );
	}

	/**
	 * Check if Confirmation Email Notification is enabled.
	 *
	 * @return bool
	 */
	public function is_confirmation_email_enabled(): bool {
		return boolval( $this->get_form()->get_form_object()->get_form_data( 'confirmation_email' ) );
	}

	/**
	 * Check if Custom Email Notification is enabled.
	 *
	 * @return bool
	 */
	public function is_custom_email_enabled(): bool {
		return boolval( $this->get_form()->get_form_object()->get_form_data( 'custom_email' ) );
	}

	/**
	 * Get the Custom Email address.
	 *
	 * @return string
	 */
	public function get_custom_email(): string {
		return (string) $this->get_form()->get_form_object()->get_form_data( 'email_recipient' );
	}

	/**
	 * Get the Admin Email Template
	 *
	 * @return int
	 */
	public function get_admin_template(): int {
		return $this->get_template( 'admin_template' );
	}

	/**
	 * Get the Confirmation Email Template
	 *
	 * @return int
	 */
	public function get_confirmation_template(): int {
		return $this->get_template( 'confirmation_template' );
	}

	/**
	 * Get the Custom Email Template
	 *
	 * @return int
	 */
	public function get_custom_template(): int {
		return $this->get_template( 'custom_template' );
	}

	/**
	 * Get an Email Template form setting.
	 *
	 * @param string $key
	 *
	 * @return int
	 */
	private function get_template( string $key ): int {
		$template = intval( $this->get_form()->get_form_object()->get_form_data( $key ) );

		if ( ! $template ) {
			return EmailTemplate::get_default_template();
		}

		return $template;
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
	 * @return mixed
	 */
	public function send_email( string $recipient, string $subject, string $body, array $headers = [], array $attachments = [] ): mixed {
		add_filter(
			'wp_mail_content_type',
			function ( string $content_type ): string {
				return 'text/html';
			}
		);

		add_action(
			'vgtfb_email_head',
			function () {
				$admin_css_path = VGTFB_PLUGIN_PATH . 'assets/css/admin.css';
				printf(
					'<style>%s</style>',
					file_get_contents( $admin_css_path )
				);
			}
		);

		return wp_mail(
			$recipient,
			$subject,
			$this->wrap_body( $body ),
			$headers,
			$attachments
		);
	}
}
