<?php
/**
 * AdminPostType Trait
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Traits;

use ACFFormBlocks\Admin\Submission;
use ACFFormBlocks\Form;
use WP_Query;

/**
 * Trait for AdminPostType
 */
trait AdminPostType {

	/**
	 * The form meta key.
	 *
	 * @var string
	 */
	protected string $form_meta_key = '_acffb_form_id';

	/**
	 * Add Filters by Form ID to Submissions.
	 *
	 * @return void
	 */
	private function admin_filters(): void {
		add_action(
			'restrict_manage_posts',
			function( string $post_type ) {
				if ( self::POST_TYPE !== $post_type ) {
					return;
				}

				$forms = $this->get_forms_list();

				if ( count( $forms ) <= 1 ) {
					return;
				}

				$selected = ! empty( $_GET['acffb_form_id'] ) ? $_GET['acffb_form_id'] : false;

				printf(
					'<select name="acffb_form_id" id="acffb_form_id">
						<option value="">%s</option>',
					esc_html__( 'All Forms', 'acf-form-blocks' )
				);

				foreach ( $forms as $form_id => $form_name ) {
					printf(
						'<option value="%s" %s>%s</option>',
						esc_attr( $form_id ),
						selected( $selected, $form_id, false ),
						esc_html( $form_name )
					);
				}

				echo '</select>';
			}
		);

		$this->apply_admin_filters();
	}

	/**
	 * Apply admin filters
	 *
	 * @return void
	 */
	private function apply_admin_filters(): void {
		add_action( 'pre_get_posts', [ $this, 'filter_by_form' ] );
	}

	/**
	 * Disable admin filters
	 *
	 * @return void
	 */
	private function disable_admin_filters(): void {
		remove_action( 'pre_get_posts', [ $this, 'filter_by_form' ] );
	}

	/**
	 * Filter Results by Form ID.
	 *
	 * @param WP_Query $query
	 *
	 * @return void
	 */
	public function filter_by_form( WP_Query $query ): void {
		global $pagenow;

		if ( ! is_admin() || self::POST_TYPE !== $query->query['post_type'] || 'edit.php' !== $pagenow || empty( $_GET['acffb_form_id'] ) || ! $query->is_main_query() ) {
			return;
		}

		$form_id     = sanitize_text_field( $_GET['acffb_form_id'] );
		$post_status = ! empty( $_GET['post_status'] ) ? sanitize_text_field( $_GET['post_status'] ) : 'publish';

		$query->set( 'post_status', $post_status );
		$query->set( 'meta_value', $form_id );

		if ( Submission::POST_TYPE === self::POST_TYPE ) {
			$query->set( 'meta_key', '_form' );
			$query->set( 'meta_compare', 'LIKE' );
		} else {
			$query->set( 'meta_key', $this->form_meta_key );
		}
	}

	/**
	 * Get the forms
	 *
	 * @return Form[]
	 */
	private function get_forms(): array {
		return Form::get_all_forms();
	}

	/**
	 * Get the forms list
	 *
	 * @return string[]
	 */
	private function get_forms_list(): array {
		$all_forms = $this->get_forms();
		$forms     = [];

		if ( ! $all_forms ) {
			return $forms;
		}

		foreach ( $all_forms as $form ) {
			$forms[ $form->get_form_object()->get_id() ] = $form->get_form_object()->get_unique_name();
		}

		return $forms;
	}

	/**
	 * Populate the Forms select field with available forms.
	 *
	 * @return void
	 */
	private function populate_forms_select(): void {
		add_filter(
			'acf/prepare_field/name=_acffb_form_id',
			function ( array $field ): array {
				$forms = $this->get_forms();

				if ( ! $forms ) {
					return $field;
				}

				foreach ( $forms as $form ) {
					$field['choices'][ $form->get_form_object()->get_id() ] = $form->get_form_object()->get_unique_name();
				}

				return $field;
			}
		);
	}

	/**
	 * Display the form name in the admin columns.
	 *
	 * @return void
	 */
	private function form_admin_column(): void {
		add_action(
			'manage_' . self::POST_TYPE . '_posts_custom_column',
			function( $column_name, $post_id ) {
				if ( '_acffb_form_id' === $column_name ) {
					$form_id = get_post_meta( $post_id, '_acffb_form_id', true );
					if ( ! $form_id ) {
						printf( '<em>%s</em>',
							esc_html__(  'Any Form', 'acf-form-blocks' )
						);
						return;
					}

					$form = Form::get_instance( $form_id );

					if ( ! $form ) {
						esc_html_e( __( 'Unknown Form', 'acf-form-blocks' ) );
						return;
					}

					printf(
						'<span title="%s">%s</span>',
						esc_attr( $form->get_form_object()->get_id() ),
						esc_html( $form->get_form_object()->get_name() )
					);

					return;
				}
			},
			10,
			2
		);
	}
}
