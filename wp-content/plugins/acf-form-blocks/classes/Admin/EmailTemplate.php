<?php
/**
 * Email Template Admin Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Admin;

use ACFFormBlocks\Form;
use ACFFormBlocks\Traits\AdminPostType;

/**
 * Email Template Admin Class
 */
class EmailTemplate {
	use AdminPostType;

	/**
	 * The Email Template post type.
	 * @var string
	 */
	const POST_TYPE = 'acffb-email-template';

	/**
	 * The Default Template ID Option Key.
	 * @var string
	 */
	const OPTION_DEFAULT_TEMPLATE_ID = 'acffb-default-email-template';

	/**
	 * Array of template IDs.
	 *
	 * @var int[]
	 */
	private array $templates = [];

	/**
	 * @var ?int
	 */
	private static ?int $default_template = null;

	/**
	 * Submission constructor.
	 */
	public function __construct() {
		// Register the post type.
		$this->register_post_type();

		// Register the Meta Fields
		$this->register_fields();

		// Populate the Forms meta select field.
		$this->populate_forms_select();

		// Populate the Form Fields meta select field.
		$this->populate_fields_select();

		// Populate the meta fields select field.
		$this->populate_meta_select();

		// Filter the Template selects by form ID.
		$this->filter_template_selects();

		// Show the form name admin column.
		$this->form_admin_column();

		// Customize admin columns
		$this->admin_columns();

		// Add Filter by Form ID.
		$this->admin_filters();

		// Prevent Deletion of the default template.
		$this->protect_default_template();
	}

	/**
	 * Register the Email Template post type.
	 *
	 * @return void
	 */
	private function register_post_type(): void {
		add_action(
			'init',
			function() {
				$labels = [
					'name'                  => _x( 'Email Templates', 'Post Type General Name', 'acf-form-blocks' ),
					'singular_name'         => _x( 'Email Template', 'Post Type Singular Name', 'acf-form-blocks' ),
					'menu_name'             => __( 'Email Templates', 'acf-form-blocks' ),
					'name_admin_bar'        => __( 'Email Template', 'acf-form-blocks' ),
					'archives'              => __( '', 'acf-form-blocks' ),
					'attributes'            => __( 'Email Template Attributes', 'acf-form-blocks' ),
					'parent_item_colon'     => __( 'Parent Email Template:', 'acf-form-blocks' ),
					'all_items'             => __( 'All Email Templates', 'acf-form-blocks' ),
					'add_new_item'          => __( 'Add New Email Template', 'acf-form-blocks' ),
					'add_new'               => __( 'Add New', 'acf-form-blocks' ),
					'new_item'              => __( 'New Email Template', 'acf-form-blocks' ),
					'edit_item'             => __( 'Edit Email Template', 'acf-form-blocks' ),
					'update_item'           => __( 'Update Email Template', 'acf-form-blocks' ),
					'view_item'             => __( 'View Email Template', 'acf-form-blocks' ),
					'view_items'            => __( 'View Email Templates', 'acf-form-blocks' ),
					'search_items'          => __( 'Search Email Template', 'acf-form-blocks' ),
					'not_found'             => __( 'Not found', 'acf-form-blocks' ),
					'not_found_in_trash'    => __( 'Not found in Trash', 'acf-form-blocks' ),
					'featured_image'        => __( 'Featured Image', 'acf-form-blocks' ),
					'set_featured_image'    => __( 'Set featured image', 'acf-form-blocks' ),
					'remove_featured_image' => __( 'Remove featured image', 'acf-form-blocks' ),
					'use_featured_image'    => __( 'Use as featured image', 'acf-form-blocks' ),
					'insert_into_item'      => __( 'Insert into email template', 'acf-form-blocks' ),
					'uploaded_to_this_item' => __( 'Uploaded to this email template', 'acf-form-blocks' ),
					'items_list'            => __( 'Email Templates list', 'acf-form-blocks' ),
					'items_list_navigation' => __( 'Email Templates list navigation', 'acf-form-blocks' ),
					'filter_items_list'     => __( 'Filter email templates list', 'acf-form-blocks' ),
				];
				$args = [
					'label'               => __( 'Email Template', 'acf-form-blocks' ),
					'description'         => __( 'ACF Form Block Email Templates', 'acf-form-blocks' ),
					'labels'              => $labels,
					'supports'            => [ 'title', 'editor', 'thumbnail', 'revisions' ],
					'hierarchical'        => false,
					'public'              => true,
					'show_ui'             => true,
					'show_in_menu'        => 'acffb-forms',
					'menu_position'       => 20,
					'menu_icon'           => 'dashicons-feedback',
					'show_in_admin_bar'   => true,
					'show_in_nav_menus'   => false,
					'can_export'          => true,
					'has_archive'         => false,
					'exclude_from_search' => true,
					'publicly_queryable'  => true,
					'rewrite'             => false,
					'capability_type'     => 'page',
					'show_in_rest'        => true,
					'template'            => [
						[ 'acf/all-fields' ]
					],
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
				'key' => 'group_669aadf85e88f',
				'title' => 'Template Settings',
				'fields' => array(
					array(
						'key' => 'field_669aadf8d2686',
						'label' => 'Form',
						'name' => '_acffb_form_id',
						'aria-label' => '',
						'type' => 'select',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'0' => 'Any Form',
						),
						'default_value' => '0',
						'return_format' => 'value',
						'multiple' => 0,
						'allow_null' => 0,
						'ui' => 1,
						'ajax' => 0,
						'placeholder' => '',
					),
					array(
						'key' => 'field_669ec6e670126',
						'label' => 'Email Subject',
						'name' => 'email_subject',
						'aria-label' => '',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'maxlength' => '',
						'placeholder' => 'New Form Submission',
						'prepend' => '',
						'append' => '',
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'acffb-email-template',
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
	 * Register custom admin columns for Email Templates
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
						$new_columns['_acffb_form_id'] = __( 'Form', 'acf-form-blocks' );
					}
				}

				return $new_columns;
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
			'acf/prepare_field/key=field_669ea8d7298be',
			function ( array $field ): array {
				$form = Form::get_instance();

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
	 * Populate the Meta select with available meta.
	 *
	 * @return void
	 */
	private function populate_meta_select(): void {
		add_filter(
			'acf/prepare_field/key=field_66a7b91132f2d',
			function ( array $field ): array {
				$form = Form::get_instance();

				if ( ! $form ) {
					return $field;
				}

				foreach ( $form->get_meta() as $meta_field ) {
					$field['choices'][ $meta_field->get_key() ] = $meta_field->get_label();
				}

				return $field;
			}
		);
	}

	/**
	 * Filter specific forms for template dropdowns.
	 *
	 * @return void
	 */
	private function filter_template_selects(): void {
		$filter = function ( array $field ): array {

			$block   = acffb_get_posted_acf_block();
			$form_id = acffb_get_block_id_from_acf_block_data( $block );

			if ( ! $form_id ) {
				return $field;
			}

			$field['choices'] = [];

			foreach ( $this->get_templates() as $template_id ) {
				$template_form_id = get_post_meta( $template_id, '_acffb_form_id', true );
				if ( $form_id && $template_form_id && $template_form_id !== $form_id ) {
					continue;
				}

				$field['choices'][ $template_id ] = get_the_title( $template_id );
				if ( $template_id === self::get_default_template() ) {
					$field['choices'][ $template_id ] .= ' (' . __( 'Default', 'acf-form-blocks' ) . ')';
				}
			}

			return $field;
		};

		// Admin Template
		add_filter( 'acf/prepare_field/key=field_669eb7e04224d', $filter );

		// Confirmation Template
		add_filter( 'acf/prepare_field/key=field_669eb8184224e', $filter );

		// Custom Template
		add_filter( 'acf/prepare_field/key=field_669eb8394224f', $filter );
	}

	/**
	 * Get Email Templates
	 *
	 * @return array
	 */
	public function get_templates(): array {
		if ( ! empty( $this->templates ) ) {
			return $this->templates;
		}

		$default_template = self::get_default_template();

		$args = [
			'post_type'      => self::POST_TYPE,
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'order'          => 'ASC',
			'orderby'        => 'title',
		];

		$templates = get_posts( $args );

		if ( ! $templates ) {
			return [];
		}

		$templates = wp_list_pluck( $templates, 'ID' );

		// Move the default template ID to the top of the array
		if ( $default_template ) {
			$default_template_index = array_search( $default_template, $templates );
			if ( $default_template_index !== false ) {
				unset( $templates[ $default_template_index ] );
				array_unshift( $templates, $default_template );
			}
		}

		$this->templates = $templates;

		return $this->templates;
	}

	/**
	 * Get the default template.
	 *
	 * @return ?int
	 */
	public static function get_default_template(): ?int {
		if ( self::$default_template ) {
			return self::$default_template;
		}

		$default_template = get_option( self::OPTION_DEFAULT_TEMPLATE_ID );

		if ( ! $default_template ) {
			$default_template = self::init_default_template();
		}

		if ( $default_template ) {
			self::$default_template = $default_template;
		}

		return self::$default_template;
	}

	/**
	 * Initialize the default template.
	 *
	 * @return ?int
	 */
	private static function init_default_template(): ?int {
		$default_template = wp_insert_post( [
			'post_title'   => __( 'All Fields', 'acf-form-blocks' ),
			'post_content' => '<!-- wp:acf/all-fields {"name":"acf/all-fields","data":{},"mode":"preview"} /-->',
			'post_status'  => 'publish',
			'post_type'    => self::POST_TYPE,
		] );

		if ( ! $default_template ) {
			return null;
		}

		update_option( self::OPTION_DEFAULT_TEMPLATE_ID, $default_template );

		return $default_template;
	}

	/**
	 * Prevent deletion of the default template
	 *
	 * @return void
	 */
	private function protect_default_template(): void {
		add_action(
			'before_delete_post',
			function( int $post_id ) {
				if ( self::get_default_template() === $post_id ) {
					wp_die( __( 'You cannot delete the default template.', 'acf-form-blocks' ) );
				}
			}
		);

		add_filter(
			'acf/pre_save_post',
			function( array $post_data ): array {
				if ( self::get_default_template() === $post_data['ID'] ) {
					unset( $post_data['post_status'] );
				}

				return $post_data;
			}
		);

		add_filter(
			'post_row_actions',
			function( array $actions, \WP_Post $post ): array {
				if ( self::get_default_template() === $post->ID ) {
					unset( $actions['trash'] );
				}

				return $actions;
			},
			10,
			2
		);

		add_filter(
			'display_post_states',
			function( array $post_states, \WP_Post $post ): array {
				if ( self::get_default_template() === $post->ID ) {
					$post_states['default_template'] = __( 'Default', 'acf-form-blocks' );
				}

				return $post_states;
			},
			10,
			2
		);

		add_action(
			'admin_head',
			function () {
				global $post;

				if ( ! $post || self::get_default_template() !== $post->ID ) {
					return;
				}

				// Not sure how stable this is. The alternative is using :last-child, but no guarantee Trash is the last item.
				echo '<style>div[role=menuitem][id=":r11:"] { display: none; }</style>';
			}
		);
	}
}
