<?php
/**
 * AdminPostType Trait
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Traits;

use ACFFormBlocks\Form;
use WP_Query;

/**
 * Trait for AdminPostType
 */
trait AdminPostType {

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

				$forms = $this->get_forms();

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

		$post_status = ! empty( $_GET['post_status'] ) ? sanitize_text_field( $_GET['post_status'] ) : 'publish';

		$query->set( 'post_status', $post_status );
		$query->set( 'meta_key', '_form_id' );
		$query->set( 'meta_value', sanitize_text_field( $_GET['acffb_form_id'] ) );
	}

	/**
	 * Get the forms with Submissions.
	 *
	 * @return array
	 */
	private function get_forms(): array {
		$forms = [];

		$post_status = ! empty( $_GET['post_status'] ) ? sanitize_text_field( $_GET['post_status'] ) : 'publish';

		$this->disable_admin_filters();
		$submissions = new WP_Query(
			[
				'post_type'      => self::POST_TYPE,
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'post_status'    => $post_status,
			]
		);
		$this->apply_admin_filters();

		foreach ( $submissions->posts as $post_id ) {
			$form = $this->get_form( $post_id );
			if ( ! $form ) {
				continue;
			}

			$form_id   = $form->get_form_object()->get_id();
			$form_name = $form->get_form_object()->get_name();

			if ( empty( $forms[ $form_id ] ) ) {
				$forms[ $form_id ] = $form_name . ' (...' . substr( $form_id, -5 ) . ')';
			} else {
				if ( ! str_contains( $forms[ $form_id ], $form_name ) ) {
					$forms[ $form_id ] .= ' ' . __( 'aka', 'acf-form-blocks' ) . ' ' . $form_name;
				}
			}
		}

		return $forms;
	}

	/**
	 * Get the form instance.
	 *
	 * @param ?int $post_id The post ID.
	 *
	 * @return ?Form
	 */
	private function get_form( ?int $post_id = null ): ?Form {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$form_meta = get_post_meta( $post_id, '_form', true );

		if ( ! $form_meta ) {
			return null;
		}

		$markup  = $form_meta['markup'] ?? '';
		$context = $form_meta['context'] ?? [];
		$form_id = $this->get_form_id( $post_id );
		$form    = Form::get_instance( $form_id, $markup, $context, 'content' );

		$form_name = get_post_meta( $post_id, '_form_name', true );
		if ( $form_name ) {
			$form->get_form_object()->set_name( $form_name );
		}

		$form->preload_meta();
		$form->get_form_object()->update_field_context();
		$form->update_cache();

		return $form;
	}

	/**
	 * Get the form ID.
	 *
	 * @param ?int $post_id The post ID.
	 *
	 * @return ?string
	 */
	private function get_form_id( ?int $post_id = null ): ?string {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$form_id = get_post_meta( $post_id, '_form_id', true );

		if ( ! $form_id ) {
			return null;
		}

		return $form_id;
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
				$forms = Form::get_all_forms();

				if ( ! $forms ) {
					return $field;
				}

				foreach ( $forms as $form ) {
					$form_name  = $form->get_form_object()->get_name();
					$short_id   = substr( $form->get_form_object()->get_id(), -5 );
					$form_name .= ' (...' . $short_id . ')';
					$field['choices'][ $form->get_form_object()->get_id() ] = $form_name;
				}

				return $field;
			}
		);
	}
}
