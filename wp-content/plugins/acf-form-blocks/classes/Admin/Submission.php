<?php
/**
 * Submission Admin Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Admin;

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Form;

/**
 * Submission Admin Class
 */
class Submission {

	/**
	 * The Form instance.
	 *
	 * @var ?Form
	 */
	private ?Form $form = null;

	/**
	 * Submission constructor.
	 */
	public function __construct() {
		$this->content_meta_box();
	}

	/**
	 * Set up the form instance.
	 *
	 * @return void
	 */
	private function get_form(): void {
		$markup     = get_post_meta( get_the_ID(), '_form_markup', true );
		$context    = get_post_meta( get_the_ID(), '_form_context', true );
		$this->form = acffb_get_form( [], $markup, $context );
	}

	/**
	 * Add the content meta box to display the form submission.
	 *
	 * @return void
	 */
	private function content_meta_box(): void {
		// Add meta box for submission data.
		add_action(
			'add_meta_boxes',
			function() {
				add_meta_box(
					'acffb_submission_data',
					__( 'Submission Data', 'acf-form-blocks' ),
					function( \WP_Post $post ): void {
						$data = $post->post_content ? json_decode( $post->post_content, true ) : [];
						if ( ! $data ) {
							printf(
								'<p>%s <strong>%s:</strong></p>',
								esc_html__( 'There was a problem rendering the submission data.', 'acf-form-blocks' ),
								esc_html__( 'Raw Submission Data', 'acf-form-blocks' )
							);

							printf(
								'<pre>%s</pre>',
								esc_html( $post->post_content )
							);
							return;
						}

						$this->render_submission_data( $data );
					},
					ACFFB_SUBMISSION_POST_TYPE,
					'normal',
					'high'
				);

				remove_meta_box( 'pageparentdiv', ACFFB_SUBMISSION_POST_TYPE, 'side' );
			}
		);
	}

	/**
	 * Render the submission data.
	 *
	 * @param array $data Submission data.
	 *
	 * @return void
	 */
	private function render_submission_data( array $data ): void {
		if ( ! $this->form ) {
			$this->get_form();
		}
		?>
		<table class="form-table acffb-submission">
			<tbody>
				<?php foreach ( $data as $key => $content ) :
					$field = $this->form->get_form_object()->get_field_by_id( $key );
					?>
					<tr id="<?php echo esc_attr( $key ); ?>">
						<th scope="row"><?php echo esc_html( $content['label'] ); ?></th>
						<td><?php $field->render_value( $content['value'], $this->form ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}
}
