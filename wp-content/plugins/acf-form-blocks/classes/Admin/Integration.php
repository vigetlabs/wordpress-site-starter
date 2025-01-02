<?php
/**
 * Integration Admin Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Admin;

use ACFFormBlocks\Traits\AdminPostType;

/**
 * Integration Admin Class
 */
class Integration {
	use AdminPostType;

	/**
	 * The Integration post type.
	 * @var string
	 */
	const POST_TYPE = 'acffb-integration';

	/**
	 * Integration constructor.
	 */
	public function __construct() {
		// Register the post type.
		$this->register_post_type();

		// Register custom fields.
		$this->register_fields();

		// Populate the forms select.
		$this->populate_forms_select();

		// Customize admin columns
		$this->admin_columns();

		// Add Submission Filters by Form ID.
		$this->admin_filters();

		// Register the meta boxes.
		$this->meta_boxes();
	}

	/**
	 * Register the Integration post type.
	 *
	 * @return void
	 */
	private function register_post_type(): void {
		add_action(
			'init',
			function() {
				$labels = [
					'name'                  => _x( 'Integrations', 'Post Type General Name', 'acf-form-blocks' ),
					'singular_name'         => _x( 'Integration', 'Post Type Singular Name', 'acf-form-blocks' ),
					'menu_name'             => __( 'Integrations', 'acf-form-blocks' ),
					'name_admin_bar'        => __( 'Integration', 'acf-form-blocks' ),
					'archives'              => '',
					'attributes'            => __( 'Integration Attributes', 'acf-form-blocks' ),
					'parent_item_colon'     => __( 'Parent Integration:', 'acf-form-blocks' ),
					'all_items'             => __( 'All Integrations', 'acf-form-blocks' ),
					'add_new_item'          => __( 'Add New Integration', 'acf-form-blocks' ),
					'add_new'               => __( 'Add New', 'acf-form-blocks' ),
					'new_item'              => __( 'New Integration', 'acf-form-blocks' ),
					'edit_item'             => __( 'Edit Integration', 'acf-form-blocks' ),
					'update_item'           => __( 'Update Integration', 'acf-form-blocks' ),
					'view_item'             => __( 'View Integration', 'acf-form-blocks' ),
					'view_items'            => __( 'View Integrations', 'acf-form-blocks' ),
					'search_items'          => __( 'Search Integration', 'acf-form-blocks' ),
					'not_found'             => __( 'No Integrations found', 'acf-form-blocks' ),
					'not_found_in_trash'    => __( 'Not found in Trash', 'acf-form-blocks' ),
					'featured_image'        => __( 'Featured Image', 'acf-form-blocks' ),
					'set_featured_image'    => __( 'Set featured image', 'acf-form-blocks' ),
					'remove_featured_image' => __( 'Remove featured image', 'acf-form-blocks' ),
					'use_featured_image'    => __( 'Use as featured image', 'acf-form-blocks' ),
					'insert_into_item'      => __( 'Insert into integration', 'acf-form-blocks' ),
					'uploaded_to_this_item' => __( 'Uploaded to this integration', 'acf-form-blocks' ),
					'items_list'            => __( 'Integrations list', 'acf-form-blocks' ),
					'items_list_navigation' => __( 'Integrations list navigation', 'acf-form-blocks' ),
					'filter_items_list'     => __( 'Filter integrations list', 'acf-form-blocks' ),
				];
				$args = [
					'label'               => __( 'Integration', 'acf-form-blocks' ),
					'description'         => __( 'ACF Form Block Integrations', 'acf-form-blocks' ),
					'labels'              => $labels,
					'supports'            => [ 'title' ],
					'hierarchical'        => false,
					'public'              => true,
					'show_ui'             => true,
					'show_in_menu'        => 'acffb-forms',
					'menu_position'       => 10,
					'menu_icon'           => 'dashicons-rest-api',
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
	 * Register the fields for the Email Template post type.
	 *
	 * @return void
	 */
	private function register_fields(): void {
		add_action( 'acf/include_fields', function() {
			if ( ! function_exists( 'acf_add_local_field_group' ) ) {
				return;
			}

			acf_add_local_field_group( array(
				'key' => 'group_6776d358e0939',
				'title' => 'Integration Settings',
				'fields' => array(
					array(
						'key' => 'field_6776d358e5c47',
						'label' => 'Form',
						'name' => '_acffb_form_id',
						'aria-label' => '',
						'type' => 'select',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'0' => 'Select Form',
						),
						'default_value' => '0',
						'return_format' => 'value',
						'multiple' => 0,
						'allow_null' => 0,
						'ui' => 1,
						'ajax' => 0,
						'placeholder' => '',
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'acffb-integration',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'side',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
				'show_in_rest' => 1,
			) );
		} );

	}

	/**
	 * Register custom admin columns for Integrations
	 *
	 * @return void
	 */
	private function admin_columns(): void {
		add_filter(
			'manage_' . self::POST_TYPE . '_posts_columns',
			function( array $columns ): array {
				$new_columns = [];
				foreach ( $columns as $key => $column ) {
					if ( 'date' === $key ) {
						continue;
					}

					$new_columns[ $key ] = 'title' === $key ? __( 'Integration', 'acf-form-blocks' ) : $column;

					if ( 'title' === $key ) {
						$new_columns['form']        = __( 'Form', 'acf-form-blocks' );
						$new_columns['third-party'] = __( 'Third-Party', 'acf-form-blocks' );
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

				if ( 'third-party' === $column_name ) {
					$third_party = get_post_meta( $post_id, '_third_party', true );
					echo $third_party ? esc_html( $third_party ) : '&mdash;';
					return;
				}
			},
			10,
			2
		);
	}

	/**
	 * Add the meta boxes to display the form integration data.
	 *
	 * @return void
	 */
	private function meta_boxes(): void {
		add_action(
			'add_meta_boxes',
			function() {
				add_meta_box(
					'acffb_integration_settings',
					__( 'Integration Settings', 'acf-form-blocks' ),
					function( \WP_Post $post ): void {
						echo 'Integration settings...';
					},
					self::POST_TYPE,
					'normal',
					'high'
				);

				add_meta_box(
					'acffb_integration_stats',
					__( 'Integration Stats', 'acf-form-blocks' ),
					function( \WP_Post $post ): void {
						$this->render_integration_stats( $post->ID );
					},
					self::POST_TYPE,
					'side'
				);

				remove_meta_box( 'pageparentdiv', self::POST_TYPE, 'side' );
			}
		);
	}

	/**
	 * Render the integration stats.
	 *
	 * @param int $post_id The Integration Post ID.
	 *
	 * @return void
	 */
	private function render_integration_stats( int $post_id ): void {
		echo 'Stats...';
	}
}
