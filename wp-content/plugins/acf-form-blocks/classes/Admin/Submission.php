<?php
/**
 * Submission Admin Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Admin;

use ACFFormBlocks\Form;
use ACFFormBlocks\Views;
use ACFFormBlocks\Traits\AdminPostType;

/**
 * Submission Admin Class
 */
class Submission {
	use AdminPostType;

	/**
	 * The Submission post type.
	 * @var string
	 */
	const POST_TYPE = 'acffb-submission';

	/**
	 * The rendered fields.
	 *
	 * @var array
	 */
	private static array $rendered = [];

	/**
	 * Submission constructor.
	 */
	public function __construct() {
		// Register the post type.
		$this->register_post_type();

		// Customize admin columns
		$this->admin_columns();

		// Add Submission Filters by Form ID.
		$this->admin_filters();

		// Register the meta boxes.
		$this->meta_boxes();
	}

	/**
	 * Register the Submission post type.
	 *
	 * @return void
	 */
	private function register_post_type(): void {
		add_action(
			'init',
			function() {
				$labels = [
					'name'                  => _x( 'Submissions', 'Post Type General Name', 'acf-form-blocks' ),
					'singular_name'         => _x( 'Submission', 'Post Type Singular Name', 'acf-form-blocks' ),
					'menu_name'             => __( 'Submissions', 'acf-form-blocks' ),
					'name_admin_bar'        => __( 'Submission', 'acf-form-blocks' ),
					'archives'              => '',
					'attributes'            => __( 'Submission Attributes', 'acf-form-blocks' ),
					'parent_item_colon'     => __( 'Parent Submission:', 'acf-form-blocks' ),
					'all_items'             => __( 'All Submissions', 'acf-form-blocks' ),
					'add_new_item'          => '',
					'add_new'               => '',
					'new_item'              => '',
					'edit_item'             => __( 'Edit Submission', 'acf-form-blocks' ),
					'update_item'           => __( 'Update Submission', 'acf-form-blocks' ),
					'view_item'             => __( 'View Submission', 'acf-form-blocks' ),
					'view_items'            => __( 'View Submissions', 'acf-form-blocks' ),
					'search_items'          => __( 'Search Submission', 'acf-form-blocks' ),
					'not_found'             => __( 'Not found', 'acf-form-blocks' ),
					'not_found_in_trash'    => __( 'Not found in Trash', 'acf-form-blocks' ),
					'featured_image'        => __( 'Featured Image', 'acf-form-blocks' ),
					'set_featured_image'    => __( 'Set featured image', 'acf-form-blocks' ),
					'remove_featured_image' => __( 'Remove featured image', 'acf-form-blocks' ),
					'use_featured_image'    => __( 'Use as featured image', 'acf-form-blocks' ),
					'insert_into_item'      => __( 'Insert into submission', 'acf-form-blocks' ),
					'uploaded_to_this_item' => __( 'Uploaded to this submission', 'acf-form-blocks' ),
					'items_list'            => __( 'Submissions list', 'acf-form-blocks' ),
					'items_list_navigation' => __( 'Submissions list navigation', 'acf-form-blocks' ),
					'filter_items_list'     => __( 'Filter submissions list', 'acf-form-blocks' ),
				];
				$args = [
					'label'               => __( 'Submission', 'acf-form-blocks' ),
					'description'         => __( 'ACF Form Block Submissions', 'acf-form-blocks' ),
					'labels'              => $labels,
					'supports'            => [ 'title' ],
					'hierarchical'        => false,
					'public'              => true,
					'show_ui'             => true,
					'show_in_menu'        => 'acffb-forms',
					'menu_position'       => 10,
					'menu_icon'           => 'dashicons-feedback',
					'show_in_admin_bar'   => true,
					'show_in_nav_menus'   => false,
					'can_export'          => true,
					'has_archive'         => false,
					'exclude_from_search' => true,
					'publicly_queryable'  => true,
					'rewrite'             => false,
					'capability_type'     => 'page',
				];

				register_post_type( self::POST_TYPE, $args );
			}
		);
	}

	/**
	 * Register custom admin columns for Submissions
	 *
	 * @return void
	 */
	private function admin_columns(): void {
		add_filter(
			'manage_' . self::POST_TYPE . '_posts_columns',
			function( array $columns ): array {
				$new_columns = [];
				foreach ( $columns as $key => $column ) {
					$new_columns[ $key ] = $column;
					if ( 'title' === $key ) {
						$new_columns['form'] = __( 'Form', 'acf-form-blocks' );
						$new_columns['page'] = __( 'Page', 'acf-form-blocks' );
					}
				}

				return $new_columns;
			}
		);

		add_action(
			'manage_' . self::POST_TYPE . '_posts_custom_column',
			function( $column_name, $post_id ) {
				if ( 'form' === $column_name ) {
					$form = $this->get_form( $post_id );
					if ( ! $form ) {
						printf(
							'<span>%s</span>',
							esc_html__( 'Unknown', 'acf-form-blocks' )
						);
						return;
					}

					printf(
						'<span title="%s">%s</span>',
						esc_attr( $form->get_form_object()->get_id() ),
						esc_html( $form->get_form_object()->get_name() )
					);
					return;
				}

				if ( 'page' === $column_name ) {
					$page_id = get_post_meta( $post_id, '_post_id', true );
					if ( $page_id ) {
						printf(
							'<a href="%s">%s</a>',
							esc_url( get_edit_post_link( $page_id ) ),
							esc_html( get_the_title( $page_id ) )
						);
					}
					return;
				}
			},
			10,
			2
		);
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
						$content = $post->post_content ? json_decode( $post->post_content, true ) : [];
						if ( ! $content ) {
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

						$this->render_submission_content( $content );
					},
					self::POST_TYPE,
					'normal',
					'high'
				);

				add_meta_box(
					'acffb_submission_meta',
					__( 'Submission Meta', 'acf-form-blocks' ),
					function( \WP_Post $post ): void {
						$this->render_submission_meta( $post->ID );
					},
					self::POST_TYPE,
					'side'
				);

				add_meta_box(
					'acffb_submission_confirmation',
					__( 'Confirmation Details', 'acf-form-blocks' ),
					function( \WP_Post $post ): void {
						$this->render_confirmation( $post->ID );
					},
					self::POST_TYPE,
					'side'
				);

				add_meta_box(
					'acffb_submission_notifications',
					__( 'Notifications', 'acf-form-blocks' ),
					function( \WP_Post $post ): void {
						$this->render_notifications( $post->ID );
					},
					self::POST_TYPE,
					'side'
				);

				remove_meta_box( 'pageparentdiv', self::POST_TYPE, 'side' );
			}
		);
	}

	/**
	 * Render the submission content.
	 *
	 * @param array $content Submission content.
	 *
	 * @return void
	 */
	private function render_submission_content( array $content ): void {
		Views::render( 'templates/all-fields', [ 'form' => $this->get_form(), 'content' => $content ] );
	}

	/**
	 * Render the submission content.
	 *
	 * @param string $field_key Field Key.
	 *
	 * @return bool
	 */
	public static function has_rendered( string $field_key ): bool {
		if ( in_array( $field_key, self::$rendered, true ) ) {
			return true;
		}

		self::$rendered[] = $field_key;
		return false;
	}

	/**
	 * Render the Submission Meta data
	 *
	 * @param int   $post_id
	 * @param array $excluded
	 * @param array $included
	 *
	 * @return void
	 */
	private function render_meta( int $post_id, array $excluded = [], array $included = [] ): void {
		$form_meta = get_post_meta( $post_id, '_form', true );
		$form      = Form::get_instance( $form_meta['id'], $form_meta['markup'], $form_meta['context'], 'content' );

		$form->load_meta( $post_id );

		foreach ( $form->get_meta() as $meta_field ) {
			if ( in_array( $meta_field->get_key(), $excluded, true ) ) {
				continue;
			}

			if ( ! empty( $included ) && ! in_array( $meta_field->get_key(), $included, true ) ) {
				continue;
			}

			$value = $meta_field->get_value( null, true );
			$label = $meta_field->get_label();

			if ( is_array( $value ) ) {
				foreach ( $value as $key => $val ) {
					if ( in_array( $key, $excluded, true ) ) {
						continue;
					}

					if ( ! empty( $included ) && ! in_array( $meta_field->get_key(), $included, true ) ) {
						continue;
					}

					printf(
						'<p><strong>%s:</strong> %s</p>',
						esc_html( $meta_field->get_label( $key ) ),
						$val ? wp_kses_post( $val ) : $val
					);
				}

				return;
			}

			printf(
				'<p><strong>%s:</strong> %s</p>',
				esc_html( $label ),
				$value ? wp_kses_post( $value ) : ''
			);
		}
	}

	/**
	 * Render the submission meta.
	 *
	 * @param int $post_id The Submission Post ID.
	 *
	 * @return void
	 */
	private function render_submission_meta( int $post_id ): void {
		$excluded = [
			'markup',
			'context',
			'_confirmation',
			'_notifications',
		];

		$this->render_meta( $post_id, $excluded );
	}

	/**
	 * Render the confirmation data.
	 *
	 * @param int $post_id The Submission Post ID.
	 *
	 * @return void
	 */
	private function render_confirmation( int $post_id ): void {
		$this->render_meta( $post_id, [], [ '_confirmation' ] );
	}

	/**
	 * Render the notifications data.
	 *
	 * @param int $post_id The Submission Post ID.
	 *
	 * @return void
	 */
	private function render_notifications( int $post_id ): void {
		$this->render_meta( $post_id, [], [ '_notifications' ] );
	}
}
