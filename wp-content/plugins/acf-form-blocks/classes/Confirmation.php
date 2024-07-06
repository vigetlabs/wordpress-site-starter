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
		$custom = $this->form->get_form_object()->get_form_data( 'custom_url' );

		if ( $custom ) {
			$redirect = $this->form->get_form_object()->get_form_data( 'redirect' );
		} else {
			$page     = $this->form->get_form_object()->get_form_data( 'page' );
			$redirect = get_permalink( $page->ID );
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
		?>
		<div class="acffb-confirmation">
			<?php if ( 'content' === $this->get_type() ) :
				$page = $this->form->get_form_object()->get_form_data( 'page' );
				echo apply_filters( 'the_content', $page->post_content );
			else :
				printf(
					'<p>%s</p>',
					wp_kses_post( $this->get_message() )
				);
			endif;
			?>
		</div>
		<?php
	}
}
