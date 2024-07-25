<?php
/**
 * Submission Admin Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Admin;

use ACFFormBlocks\Form;
use WP_Query;

/**
 * Submission Admin Class
 */
class Submission {

	/**
	 * The Submission post type.
	 * @var string
	 */
	const POST_TYPE = 'acffb-submission';

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

		if ( ! $form_id ) {
			return null;
		}

		return $form_id;
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
		$form      = Form::get_instance( $form_meta['id'], $form_meta['markup'], $form_meta['context'] );

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
						esc_html( $val )
					);
				}

				return;
			}

			printf(
				'<p><strong>%s:</strong> %s</p>',
				esc_html( $label ),
				esc_html( $value )
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
