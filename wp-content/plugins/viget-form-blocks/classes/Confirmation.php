<?php
/**
 * Form Confirmation
 *
 * @package VigetFormBlocks
 */

namespace VigetFormBlocks;

/**
 * Form Confirmation
 */
class Confirmation {

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

	/**
	 * Add the hooks.
	 *
	 * @return void
	 */
	private function add_hooks(): void {
		// Handle redirect.
		add_action( 'vgtfb_process_submission', [ $this, 'handle_redirect' ], 999 );
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
	 * Handle the redirect.
	 *
	 * @return void
	 */
	public function handle_redirect(): void {
		if ( 'redirect' !== $this->get_type() ) {
			return;
		}

		wp_safe_redirect( $this->get_redirect() );
		exit;
	}

	/**
	 * Get the confirmation type.
	 *
	 * @return string
	 */
	public function get_type(): string {
		$confirmation = $this->get_form()?->get_form_object()->get_form_data( 'confirmation' );

		if ( ! $confirmation ) {
			return 'message';
		}

		return $confirmation;
	}

	/**
	 * Get the confirmation message.
	 *
	 * @return string
	 */
	public function get_message(): string {
		$message = $this->get_form()?->get_form_object()->get_form_data( 'message' );

		if ( ! $message ) {
			return __( 'Thank you for your submission.', 'viget-form-blocks' );
		}

		return $message;
	}

	/**
	 * Get the redirect URL.
	 *
	 * @return string
	 */
	public function get_redirect(): string {
		$form   = $this->get_form();
		$custom = $form?->get_form_object()->get_form_data( 'custom_url' );

		if ( $custom ) {
			$redirect = $form?->get_form_object()->get_form_data( 'redirect' );
		} else {
			$page     = $form?->get_form_object()->get_form_data( 'page' );
			$redirect = $page ? get_permalink( $page->ID ) : false;
		}

		if ( ! $redirect ) {
			return '';
		}

		return $redirect;
	}

	/**
	 * Render the confirmation.
	 *
	 * @return void
	 */
	public function render(): void {
		$form = $this->get_form();
		?>
		<div class="acffb-confirmation">
			<?php if ( 'content' === $this->get_type() ) {
				$page = $form?->get_form_object()->get_form_data( 'page' );
				if ( $page ) {
					$page = is_numeric( $page ) ? get_post( $page ) : $page;
					echo apply_filters( 'the_content', $page?->post_content );
				}
			} else {
				printf(
					'<p>%s</p>',
					wp_kses_post($this->get_message())
				);
			}
			?>
		</div>
		<?php
	}
}
