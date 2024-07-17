<?php
/**
 * Submission Admin Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Admin;

use ACFFormBlocks\Form;

/**
 * Submission Admin Class
 */
class Submission {

	/**
	 * Submission constructor.
	 */
	public function __construct() {
		$this->meta_boxes();
	}

	/**
	 * Get the form instance.
	 *
	 * @return ?Form
	 */
	private function get_form(): ?Form {
		$markup  = get_post_meta( get_the_ID(), '_form_markup', true );
		$context = get_post_meta( get_the_ID(), '_form_context', true );
		$form    = Form::get_instance( null, $markup, $context );

		$form->preload_meta();
		$form->get_form_object()->update_field_context();
		$form->update_cache();

		return $form;
	}

	/**
	 * Add the meta boxes to display the form submission data.
	 *
	 * @return void
	 */
	private function meta_boxes(): void {
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

				add_meta_box(
					'acffb_submission_confirmation',
					__( 'Confirmation Details', 'acf-form-blocks' ),
					function( \WP_Post $post ): void {
						$meta = get_post_meta( $post->ID );

						if ( empty( $meta['_confirmation'] ) ) {
							printf(
								'<p>%s</p>',
								esc_html__( 'No data available.', 'acf-form-blocks' )
							);
							return;
						}

						$confirmation = $meta['_confirmation'][0];
						$confirmation = unserialize( $confirmation );

						$this->render_confirmation( $confirmation );
					},
					ACFFB_SUBMISSION_POST_TYPE,
					'side'
				);

				add_meta_box(
					'acffb_submission_notifications',
					__( 'Notifications', 'acf-form-blocks' ),
					function( \WP_Post $post ): void {
						$meta = get_post_meta( $post->ID );

						if ( empty( $meta['_notifications'] ) ) {
							printf(
								'<p>%s</p>',
								esc_html__( 'No data available.', 'acf-form-blocks' )
							);
							return;
						}

						$notifications = $meta['_notifications'][0];
						$notifications = unserialize( $notifications );

						$this->render_notifications( $notifications );
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
					$label = $field->get_field_label() ?: $content['label'];
					?>
					<tr id="<?php echo esc_attr( $key ); ?>">
						<th scope="row" title="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></th>
						<td title="<?php echo esc_attr( $field->get_label() ); ?>"><?php $field->render_value( $content['value'], $form ); ?></td>
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
			$excluded = [
				'_form_markup',
				'_form_context',
				'_confirmation',
				'_notifications',
				'_edit_lock',
			];
			if ( in_array( $key, $excluded, true ) ) {
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

	/**
	 * Render the confirmation data.
	 *
	 * @param array $confirmations Confirmations.
	 *
	 * @return void
	 */
	private function render_confirmation( array $confirmations ): void {
		foreach ( $confirmations as $key => $value ) {
			$label = ltrim( $key, '_' );
			$label = ucwords( str_replace( '_', ' ', $label ) );
			$label = str_replace( [ 'Id', 'Url', 'Ip' ], [ 'ID', 'URL', 'IP' ], $label );

			printf(
				'<p><strong>%s:</strong> %s</p>',
				esc_html( $label ),
				esc_html( $value )
			);
		}
	}

	/**
	 * Render the notifications data.
	 *
	 * @param array $notifications Notifications.
	 *
	 * @return void
	 */
	private function render_notifications( array $notifications ): void {
		foreach ( $notifications as $key => $value ) {
			$label = ltrim( $key, '_' );
			$label = ucwords( str_replace( '_', ' ', $label ) );

			$bools = [
				'admin',
				'confirmation',
				'custom',
			];

			if ( in_array( $key, $bools, true ) ) {
				$value = $value ? __( 'Yes', 'acf-form-blocks' ) : __( 'No', 'acf-form-blocks' );
			}

			printf(
				'<p><strong>%s:</strong> %s</p>',
				esc_html( $label ),
				esc_html( $value )
			);
		}
	}
}
