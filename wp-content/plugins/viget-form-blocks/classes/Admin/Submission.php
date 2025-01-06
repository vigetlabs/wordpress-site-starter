<?php
/**
 * Submission Admin Class
 *
 * @package VigetFormBlocks
 */

namespace VigetFormBlocks\Admin;

use VigetFormBlocks\Form;
use VigetFormBlocks\Views;
use VigetFormBlocks\Traits\AdminPostType;
use WP_Query;

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
					'name'                  => _x( 'Submissions', 'Post Type General Name', 'viget-form-blocks' ),
					'singular_name'         => _x( 'Submission', 'Post Type Singular Name', 'viget-form-blocks' ),
					'menu_name'             => __( 'Submissions', 'viget-form-blocks' ),
					'name_admin_bar'        => __( 'Submission', 'viget-form-blocks' ),
					'archives'              => '',
					'attributes'            => __( 'Submission Attributes', 'viget-form-blocks' ),
					'parent_item_colon'     => __( 'Parent Submission:', 'viget-form-blocks' ),
					'all_items'             => __( 'All Submissions', 'viget-form-blocks' ),
					'add_new_item'          => '',
					'add_new'               => '',
					'new_item'              => '',
					'edit_item'             => __( 'Edit Submission', 'viget-form-blocks' ),
					'update_item'           => __( 'Update Submission', 'viget-form-blocks' ),
					'view_item'             => __( 'View Submission', 'viget-form-blocks' ),
					'view_items'            => __( 'View Submissions', 'viget-form-blocks' ),
					'search_items'          => __( 'Search Submission', 'viget-form-blocks' ),
					'not_found'             => __( 'Not found', 'viget-form-blocks' ),
					'not_found_in_trash'    => __( 'Not found in Trash', 'viget-form-blocks' ),
					'featured_image'        => __( 'Featured Image', 'viget-form-blocks' ),
					'set_featured_image'    => __( 'Set featured image', 'viget-form-blocks' ),
					'remove_featured_image' => __( 'Remove featured image', 'viget-form-blocks' ),
					'use_featured_image'    => __( 'Use as featured image', 'viget-form-blocks' ),
					'insert_into_item'      => __( 'Insert into submission', 'viget-form-blocks' ),
					'uploaded_to_this_item' => __( 'Uploaded to this submission', 'viget-form-blocks' ),
					'items_list'            => __( 'Submissions list', 'viget-form-blocks' ),
					'items_list_navigation' => __( 'Submissions list navigation', 'viget-form-blocks' ),
					'filter_items_list'     => __( 'Filter submissions list', 'viget-form-blocks' ),
				];
				$args = [
					'label'               => __( 'Submission', 'viget-form-blocks' ),
					'description'         => __( 'Form Block Submissions', 'viget-form-blocks' ),
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
						$new_columns['form'] = __( 'Form', 'viget-form-blocks' );
						$new_columns['page'] = __( 'Page', 'viget-form-blocks' );
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
							'<span title="%s">%s</span>',
							esc_attr( $this->get_form_id( $post_id ) ),
							esc_html__( 'Unknown', 'viget-form-blocks' )
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
	 * Get the forms list with Submissions.
	 *
	 * @return array
	 */
	private function get_forms_list(): array {
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
					$forms[ $form_id ] .= ' ' . __( 'aka', 'viget-form-blocks' ) . ' ' . $form_name;
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
		$form    = Form::get_instance( $form_id, $markup, $context );

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

		if ( $form_id ) {
			return $form_id;
		}

		$form_meta = get_post_meta( $post_id, '_form', true );

		return ! empty( $form_meta['id'] ) ? $form_meta['id'] : null;
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
					'vgtfb_submission_data',
					__( 'Submission Data', 'viget-form-blocks' ),
					function( \WP_Post $post ): void {
						$content = $post->post_content ? json_decode( $post->post_content, true ) : [];
						if ( ! $content ) {
							printf(
								'<p>%s <strong>%s:</strong></p>',
								esc_html__( 'There was a problem rendering the submission data.', 'viget-form-blocks' ),
								esc_html__( 'Raw Submission Data', 'viget-form-blocks' )
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
					'vgtfb_submission_meta',
					__( 'Submission Meta', 'viget-form-blocks' ),
					function( \WP_Post $post ): void {
						$this->render_submission_meta( $post->ID );
					},
					self::POST_TYPE,
					'side'
				);

				add_meta_box(
					'vgtfb_submission_confirmation',
					__( 'Confirmation Details', 'viget-form-blocks' ),
					function( \WP_Post $post ): void {
						$this->render_confirmation( $post->ID );
					},
					self::POST_TYPE,
					'side'
				);

				add_meta_box(
					'vgtfb_submission_notifications',
					__( 'Notifications', 'viget-form-blocks' ),
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
