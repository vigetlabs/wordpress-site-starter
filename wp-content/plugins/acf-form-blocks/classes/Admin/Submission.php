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
	 * Get the form instance.
	 *
	 * @return ?Form
	 */
	private function get_form(): ?Form {
		$markup  = get_post_meta( get_the_ID(), '_form_markup', true );
		$context = get_post_meta( get_the_ID(), '_form_context', true );

		return Form::get_instance( null, $markup, $context );
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

				add_meta_box(
					'acffb_submission_meta',
					__( 'Submission Meta', 'acf-form-blocks' ),
					function( \WP_Post $post ): void {
						$meta = get_post_meta( $post->ID );

						$this->render_submission_meta( $meta );
					},
					ACFFB_SUBMISSION_POST_TYPE,
					'side'
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
		$form = $this->get_form();
		?>
		<table class="form-table acffb-submission">
			<tbody>
				<?php foreach ( $data as $key => $content ) :
					$field = $form->get_form_object()->get_field_by_id( $key );
					?>
					<tr id="<?php echo esc_attr( $key ); ?>">
						<th scope="row" title="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $content['label'] ); ?></th>
						<td><?php $field->render_value( $content['value'], $form ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Render the submission meta.
	 *
	 * @param array $meta Submission meta.
	 *
	 * @return void
	 */
	private function render_submission_meta( array $meta ): void {
		foreach ( $meta as $key => $value ) {
			if ( in_array( $key, [ '_form_markup', '_form_context', '_edit_lock' ], true ) ) {
				continue;
			}

			$label = ltrim( $key, '_' );
			$label = ucwords( str_replace( '_', ' ', $label ) );
			$label = str_replace( [ 'Id', 'Url', 'Ip' ], [ 'ID', 'URL', 'IP' ], $label );

			printf(
				'<p><strong>%s:</strong> %s</p>',
				esc_html( $label ),
				esc_html( $value[0] )
			);
		}
	}
}
