<?php
/**
 * Integration Admin Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Admin;

use ACFFormBlocks\Form;
use ACFFormBlocks\Integrations\Integration as BaseIntegration;
use ACFFormBlocks\Traits\AdminPostType;
use WP_Query;

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

		// Populate the Form Fields meta select field.
		$this->populate_fields_select();

		// Show the form name admin column.
		$this->form_admin_column();

		// Customize admin columns
		$this->admin_columns();

		// Add Filter by Form ID.
		$this->admin_filters();

		// Register the meta boxes.
		$this->meta_boxes();

		// Watch for the integration.
		$this->handle_integration_test();

		// Temp Fix the post name. TODO: Debug why post slug is being set to the form name.
		add_filter(
			'wp_insert_post_data',
			function ( array $data ): array {
				if ( self::POST_TYPE !== $data['post_type'] ) {
					return $data;
				}

				$data['post_name'] = sanitize_title( $data['post_title'] );

				return $data;
			},
			99
		);
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
							0 => 'Select a Form',
						),
						'default_value' => 0,
						'return_format' => 'value',
						'multiple' => 0,
						'allow_null' => 0,
						'allow_in_bindings' => 1,
						'ui' => 1,
						'ajax' => 0,
						'placeholder' => '',
					),
					array(
						'key' => 'field_6776f6421ad24',
						'label' => 'Request Details',
						'name' => 'request',
						'aria-label' => '',
						'type' => 'group',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'layout' => 'block',
						'sub_fields' => array(
							array(
								'key' => 'field_6776f6611ad25',
								'label' => 'Request URL',
								'name' => 'url',
								'aria-label' => '',
								'type' => 'url',
								'instructions' => '',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'allow_in_bindings' => 0,
								'placeholder' => 'https://',
							),
							array(
								'key' => 'field_6776f67a1ad26',
								'label' => 'Request Method',
								'name' => 'method',
								'aria-label' => '',
								'type' => 'select',
								'instructions' => '',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '50',
									'class' => '',
									'id' => '',
								),
								'choices' => array(
									'GET' => 'GET',
									'POST' => 'POST',
									'PUT' => 'PUT',
									'PATCH' => 'PATCH',
									'DELETE' => 'DELETE',
								),
								'default_value' => 'POST',
								'return_format' => 'value',
								'multiple' => 0,
								'allow_null' => 0,
								'allow_in_bindings' => 0,
								'ui' => 0,
								'ajax' => 0,
								'placeholder' => '',
							),
							array(
								'key' => 'field_6776f6aa1ad27',
								'label' => 'Request Format',
								'name' => 'format',
								'aria-label' => '',
								'type' => 'select',
								'instructions' => '',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '50',
									'class' => '',
									'id' => '',
								),
								'choices' => array(
									'JSON' => 'JSON',
									'Raw' => 'Raw',
								),
								'default_value' => 'JSON',
								'return_format' => 'value',
								'multiple' => 0,
								'allow_null' => 0,
								'allow_in_bindings' => 0,
								'ui' => 0,
								'ajax' => 0,
								'placeholder' => '',
							),
						),
					),
					array(
						'key' => 'field_6776f7bf1ad28',
						'label' => 'Request Headers',
						'name' => 'headers',
						'aria-label' => '',
						'type' => 'repeater',
						'instructions' => 'Optional',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'layout' => 'block',
						'pagination' => 0,
						'min' => 0,
						'max' => 0,
						'collapsed' => '',
						'button_label' => 'Add Header',
						'rows_per_page' => 20,
						'sub_fields' => array(
							array(
								'key' => 'field_6776f7d41ad29',
								'label' => 'Header Name',
								'name' => 'name',
								'aria-label' => '',
								'type' => 'text',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '50',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'maxlength' => '',
								'allow_in_bindings' => 0,
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'parent_repeater' => 'field_6776f7bf1ad28',
							),
							array(
								'key' => 'field_6776f7f21ad2a',
								'label' => 'Header Value',
								'name' => 'value',
								'aria-label' => '',
								'type' => 'text',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '50',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'maxlength' => '',
								'allow_in_bindings' => 0,
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'parent_repeater' => 'field_6776f7bf1ad28',
							),
						),
					),
					array(
						'key' => 'field_6776f8361ad2b',
						'label' => 'Field Mapping',
						'name' => 'mapping',
						'aria-label' => '',
						'type' => 'repeater',
						'instructions' => 'Leave empty to send all field data.',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_6776d358e5c47',
									'operator' => '!=',
									'value' => '0',
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'layout' => 'block',
						'pagination' => 0,
						'min' => 0,
						'max' => 0,
						'collapsed' => '',
						'button_label' => 'Add Field',
						'rows_per_page' => 20,
						'sub_fields' => array(
							array(
								'key' => 'field_6776f8701ad2c',
								'label' => 'Form Field',
								'name' => 'field',
								'aria-label' => '',
								'type' => 'select',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '50',
									'class' => '',
									'id' => '',
								),
								'choices' => array(
									'0' => 'Select a Field',
								),
								'default_value' => '0',
								'return_format' => 'value',
								'multiple' => 0,
								'allow_null' => 0,
								'allow_in_bindings' => 0,
								'ui' => 0,
								'ajax' => 0,
								'placeholder' => '',
								'parent_repeater' => 'field_6776f8361ad2b',
							),
							array(
								'key' => 'field_6776f8d51ad2d',
								'label' => 'Map Name',
								'name' => 'key',
								'aria-label' => '',
								'type' => 'text',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '50',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'maxlength' => '',
								'allow_in_bindings' => 0,
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'parent_repeater' => 'field_6776f8361ad2b',
							),
						),
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
				'position' => 'normal',
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
						$new_columns['_acffb_form_id'] = __( 'Form', 'acf-form-blocks' );
						$new_columns['third-party']    = __( 'Third-Party', 'acf-form-blocks' );
					}
				}

				return $new_columns;
			}
		);

		add_action(
			'manage_' . self::POST_TYPE . '_posts_custom_column',
			function( $column_name, $post_id ) {
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
//				add_meta_box(
//					'acffb_integration_settings',
//					__( 'Integration Settings', 'acf-form-blocks' ),
//					function( \WP_Post $post ): void {
//						echo 'Integration settings...';
//					},
//					self::POST_TYPE,
//					'normal',
//					'high'
//				);

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
		printf(
			'<p>
				<button class="button button-secondary" id="acffb-integration-test" data-integration-id="%s">%s</button> &nbsp;
				<span id="acffb-integration-test-response"></span>
			</p>',
			esc_attr( $post_id ),
			esc_html__( 'Send Test Request', 'acf-form-blocks' )
		);
	}

	/**
	 * Handle the integration test request.
	 *
	 * @return void
	 */
	private function handle_integration_test(): void {
		add_action(
			'wp_ajax_acffb_integration_test',
			function() {
				if ( ! current_user_can( 'edit_posts' ) ) {
					wp_send_json_error( [ 'message' => __( 'Unauthorized.', 'acf-form-blocks' ) ] );
				}

				if ( ! check_ajax_referer( 'acffb', 'nonce', false ) ) {
					wp_send_json_error( [ 'message' => __( 'Invalid nonce.', 'acf-form-blocks' ) ] );
				}

				$integration_id = ! empty( $_POST['integrationId'] ) ? intval( sanitize_text_field( $_POST['integrationId'] ) ) : 0;

				if ( ! $integration_id ) {
					wp_send_json_error( [ 'message' => __( 'Integration not found.', 'acf-form-blocks' ) ] );
				}

				$integration = self::factory( $integration_id );
				$response    = $integration->test();

				if ( is_wp_error( $response ) ) {
					wp_send_json_error( [ 'message' => $response->get_error_message() ] );
				}

				$status = wp_remote_retrieve_response_code( $response );

				wp_send_json_success( [ 'message' => $status ] );
			}
		);
	}

	/**
	 * Populate the Form Field select with available fields.
	 *
	 * @return void
	 */
	private function populate_fields_select(): void {
		add_filter(
			'acf/prepare_field/key=field_6776f8701ad2c',
			function ( array $field ): array {
				$form_id = $this->get_current_form();
				$form    = Form::find_form( $form_id );

				if ( ! $form ) {
					$field['choices'] = [
						'' => __( 'Select a form and update to see fields.', 'acf-form-blocks' ),
					];
					return $field;
				}

				foreach ( $form->get_form_object()->get_all_fields() as $form_field ) {
					$field['choices'][ $form_field->get_id() ] = $form_field->get_label();
				}

				return $field;
			}
		);
	}

	/**
	 * Get the current Integration Form ID
	 *
	 * @return ?string
	 */
	private function get_current_form(): ?string {
		$post_id = ! empty( $_GET['post'] ) ? (int) $_GET['post'] : null;
		if ( ! $post_id ) {
			return null;
		}

		return get_field( '_acffb_form_id', $post_id ) ?: null;
	}

	/**
	 * Get the integrations for the form.
	 *
	 * @param string $form_id The Form ID.
	 *
	 * @return array
	 */
	public static function get_integrations( string $form_id ): array {
		$query = new WP_Query(
			[
				'post_type'      => self::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'meta_query'     => [
					[
						'key'   => '_acffb_form_id',
						'value' => $form_id,
					],
				],
			]
		);

		if ( ! $query->have_posts() ) {
			return [];
		}

		$integrations = [];

		while ( $query->have_posts() ) {
			$query->the_post();
			$integrations[] = self::factory( get_the_ID() );
		}
		wp_reset_postdata();

		return $integrations;
	}

	/**
	 * Get the Integration Type
	 *
	 * @param ?int $post_id
	 *
	 * @return string
	 */
	public static function get_type( ?int $post_id = null ): string {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$type = get_post_meta( $post_id, '_acffb_type', true );

		if ( ! $type ) {
			return 'request';
		}

		return $type;
	}

	/**
	 * Get the Integration instance by type
	 *
	 * @param int $id The Integration ID.
	 *
	 * @return BaseIntegration
	 */
	public static function factory( int $id ): BaseIntegration {
		$type = self::get_type( $id );

		if ( str_contains( $type, '\\' ) ) {
			$class = $type;
		} else {
			$class = 'ACFFormBlocks\\Integrations\\' . ucfirst( $type );
		}

		if ( class_exists( $class ) ) {
			return new $class( $id );
		}

		return new BaseIntegration( $id );
	}
}
