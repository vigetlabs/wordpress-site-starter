<?php
/**
 * Form Confirmation
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks;

/**
 * Form Confirmation
 */
class Confirmation {

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
	 * Get the confirmation type.
	 *
	 * @return string
	 */
	public function get_type(): string {
		$confirmation = $this->form->get_form_object()->get_form_data( 'confirmation' );

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
		$message = $this->form->get_form_object()->get_form_data( 'message' );

		if ( ! $message ) {
			return __( 'Thank you for your submission.', 'acf-form-blocks' );
		}

		return $message;
	}

	/**
	 * Get the redirect URL.
	 *
	 * @return string
	 */
	public function get_redirect(): string {
		$redirect = $this->form->get_form_object()->get_form_data( 'redirect' );

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
		?>
		<div class="acffb-confirmation">
			<?php if ( 'message' === $this->get_type() ) : ?>
				<p><?php echo esc_html( $this->get_message() ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}
}
