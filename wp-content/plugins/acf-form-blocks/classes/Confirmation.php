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
	 * Confirmation type.
	 *
	 * @var string
	 */
	protected string $type;

	/**
	 * Constructor.
	 *
	 * @param Form $form The Form.
	 */
	public function __construct( Form $form ) {
		$this->form = $form;
		$this->type = get_field( 'confirmation' );
	}

	/**
	 * Get the confirmation type.
	 *
	 * @return string
	 */
	public function get_type(): string {
		return $this->type;
	}

	/**
	 * Get the confirmation message.
	 *
	 * @return string
	 */
	public function get_message(): string {
		return get_field( 'message' );
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