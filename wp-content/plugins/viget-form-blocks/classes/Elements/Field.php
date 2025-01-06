<?php
/**
 * Field Class
 *
 * @package VigetFormBlocks
 */

namespace VigetFormBlocks\Elements;

use VigetFormBlocks\Form;
use WP_Block;

/**
 * Class for Fields
 */
class Field {

	/**
	 * Block data.
	 *
	 * @var array
	 */
	protected array $block;

	/**
	 * Block context.
	 *
	 * @var array
	 */
	protected array $context;

	/**
	 * Parent field ID.
	 *
	 * @var ?string
	 */
	protected ?string $parent_id = null;

	/**
	 * The Pattern ID if any.
	 *
	 * @var ?int
	 */
	protected ?int $pattern_id = null;

	/**
	 * WP Block instance.
	 *
	 * @var ?WP_Block
	 */
	protected ?WP_Block $wp_block = null;

	/**
	 * Default value.
	 *
	 * @var mixed
	 */
	protected mixed $default_value = null;

	/**
	 * Required marker placement.
	 *
	 * @var string
	 */
	protected string $req_marker_placement = 'after';

	/**
	 * Constructor.
	 *
	 * @param array    $block Block data.
	 * @param array     $context Block context.
	 * @param ?WP_Block $wp_block WP Block object.
	 */
	public function __construct( array $block, array $context = [], ?WP_Block $wp_block = null ) {
		$this->block    = $block;
		$this->context  = $context;
		$this->wp_block = $wp_block;

		if ( ! empty( $block['parent_id'] ) ) {
			$this->parent_id = $block['parent_id'];
		}
		if ( ! empty( $block['pattern_id'] ) ) {
			$this->pattern_id = $block['pattern_id'];
		}
	}

	/**
	 * Get attributes for this field.
	 *
	 * @return array
	 */
	public function get_attrs(): array {
		$attrs = [
			'id'   => $this->get_id_attr(),
			'name' => $this->get_name_attr(),
		];

		if ( $this->get_placeholder() ) {
			$attrs['placeholder'] = $this->get_placeholder();
		}

		if ( $this->is_required() ) {
			$attrs['required'] = true;
		}

		$logic = $this->get_conditional_logic();
		if ( ! is_admin() && $logic ) {
			$attrs['data-conditional-rules'] = wp_json_encode( $logic );
		}

		return $attrs;
	}

	/**
	 * Get the field type.
	 *
	 * @return ?string
	 */
	public function get_type(): ?string {
		return $this->get_field_data( 'type' );
	}

	/**
	 * Factory method to create a new field object.
	 *
	 * @param array     $block Block data.
	 * @param array     $context Context data.
	 * @param ?WP_Block $wp_block WP Block object.
	 * @param ?Form     $form The Form Element object.
	 *
	 * @return Field
	 */
	public static function factory( array $block, array $context = [], ?WP_Block $wp_block = null, ?Form $form = null ): Field {
		$element = str_replace( 'acf/', '', $block['name'] );
		$class   = __NAMESPACE__ . '\\' . ucfirst( $element );

		if ( class_exists( $class ) ) {
			// Input handler.
			if ( 'input' === $element ) {
				$input = new $class( $block, $context, $wp_block, $form );
				$type  = __NAMESPACE__ . '\\' . ucfirst( $input->get_input_type() );

				if ( class_exists( $type ) ) {
					return new $type( $block, $context, $wp_block, $form );
				}
			}

			return new $class( $block, $context, $wp_block, $form );
		}

		return new Field( $block, $context, $wp_block, $form );
	}

	/**
	 * Get the block array.
	 *
	 * @return array
	 */
	public function get_block(): array {
		return $this->block;
	}

	/**
	 * Get the block context.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function get_context( string $key = '' ): mixed {
		$context = $this->context ?? $this->wp_block?->context ?? [];

		if ( ! $key ) {
			return $context;
		}

		if ( ! isset( $context[ $key ] ) ) {
			return null;
		}

		return $context[ $key ];
	}

	/**
	 * Set the field context manually.
	 *
	 * @param array $new_context
	 *
	 * @return void
	 */
	public function set_context( array $new_context ): void {
		$context = $this->get_context();

		if ( empty( $context ) ) {
			$this->context = $new_context;
			return;
		}

		$this->context = array_merge( $context, $new_context );
	}

	/**
	 * Get the form object.
	 *
	 * @return ?Form
	 */
	public function get_form(): ?Form {
		return Form::find_form( $this->get_context( 'acffb/formId' ) );
	}

	/**
	 * Get the fieldset.
	 *
	 * @return ?Field
	 */
	public function get_fieldset(): ?Field {
		if ( ! $this->get_context( 'acffb/fieldsetId' ) ) {
			return null;
		}

		$fieldset_id = 'acf_field_' . $this->get_context( 'acffb/fieldsetId' );
		return $this->get_form()?->get_form_object()->get_field_by_id( $fieldset_id );
	}

	/**
	 * Get the parent field.
	 *
	 * @return ?Field
	 */
	public function get_parent_field(): ?Field {
		if ( ! $this->get_context( 'acffb/fieldId' ) ) {
			return null;
		}

		$input_id = 'acf_field_' . $this->get_context( 'acffb/fieldId' );

		return $this->get_form()?->get_form_object()->get_field_by_id( $input_id );
	}

	/**
	 * Get the block ACF ID
	 *
	 * @return string
	 */
	public function get_acf_id(): string {
		return get_block_id( $this->block, true );
	}

	/**
	 * Get the field ID attribute.
	 *
	 * @return string
	 */
	public function get_id_attr(): string {
		if ( ! empty( $this->block['anchor'] ) ) {
			return $this->block['anchor'];
		}

		return $this->get_name();
	}

	/**
	 * Get the field unique ID.
	 *
	 * @return string
	 */
	public function get_id(): string {
		return $this->get_name();
	}

	/**
	 * Get the field name (the actual ID).
	 *
	 * @return string
	 */
	public function get_name(): string {
		if ( empty( $this->block['blockId'] ) ) {
			return $this->get_acf_id();
		}

		$block_name = $this->get_block_name( true );
		$block_name = str_replace( '/', '_', $block_name );
		$prefix     = 'acf_form' === $block_name ? $block_name : 'acf_field';
		return $prefix . '_' . $this->block['blockId'];
	}

	/**
	 * Get the name attribute.
	 *
	 * @return string
	 */
	public function get_name_attr(): string {
		if ( $this->get_fieldset() ) {
			return sprintf(
				'%s[%s]',
				$this->get_fieldset()->get_name(),
				$this->get_name()
			);
		}

		return $this->get_name();
	}

	/**
	 * Get the block name
	 *
	 * @param bool $real If actual block name is needed.
	 *
	 * @return string
	 */
	public function get_block_name( bool $real = false ): string {
		$name = $this->block['blockName'] ?? $this->block['name'] ?? '';

		if ( $real ) {
			return $name;
		}

		return str_replace( 'acf/', '', $name );
	}

	/**
	 * Get the default value.
	 *
	 * @return mixed
	 */
	public function get_default_value(): mixed {
		return $this->default_value;
	}

	/**
	 * Set the default value.
	 *
	 * @param mixed $value
	 *
	 * @return void
	 */
	public function set_default_value( mixed $value ): void {
		$this->default_value = $value;
	}

	/**
	 * Get the field value.
	 *
	 * @return mixed
	 */
	public function get_value(): mixed {
		$value = $this->get_form()?->get_submission()->get_field_data( $this->get_name() );
		return $value ?: $this->get_default_value();
	}

	/**
	 * Get the dummy value.
	 *
	 * @return mixed
	 */
	public function get_dummy_value(): mixed {
		if ( $this->get_value() ) {
			return $this->get_value();
		}

		return 'Abc123';
	}

	/**
	 * Get the field label.
	 *
	 * @return string
	 */
	public function get_field_label(): string {
		if ( ! empty( $this->block['metadata']['name'] ) ) {
			return trim( $this->block['metadata']['name'] );
		}

		return $this->get_label();
	}

	/**
	 * Get the field label.
	 *
	 * @return string
	 */
	public function get_label(): string {
		$inner_blocks = [];

		if ( ! empty( $this->wp_block->parsed_block['innerBlocks'] ) ) {
			$inner_blocks = $this->wp_block->parsed_block['innerBlocks'];
		} elseif ( ! empty( $this->block['wp_block']['innerBlocks'] ) ) {
			$inner_blocks = $this->block['wp_block']['innerBlocks'];
		}

		if ( ! empty( $inner_blocks ) ) {
			$label = $this->find_label( $inner_blocks );

			if ( $label ) {
				return $label;
			}
		}

		return $this->get_id();
	}

	/**
	 * Find the label in the inner blocks.
	 *
	 * @param array $inner_blocks
	 *
	 * @return string
	 */
	private function find_label( array $inner_blocks ): string {
		$label = '';

		foreach ( $inner_blocks as $inner_block ) {
			if ( 'core/paragraph' === $inner_block['blockName'] ) {
				$label = wp_strip_all_tags( $inner_block['innerHTML'] );
				break;
			}

			if ( 'submit' === $this->get_block_name() && 'core/button' === $inner_block['blockName'] ) {
				$label = wp_strip_all_tags( $inner_block['innerHTML'] );
				break;
			}

			$label = wp_strip_all_tags( $inner_block['innerHTML'] );

			if ( $label ) {
				break;
			}

			if ( empty( $inner_block['innerBlocks'] ) ) {
				continue;
			}

			$nested_blocks = [
				'acf/legend',
				'acf/label',
				'core/buttons',
				'core/group',
			];

			if ( in_array( $inner_block['blockName'], $nested_blocks, true ) ) {
				$label = $this->find_label( $inner_block['innerBlocks'] );

				if ( $label ) {
					break;
				}
			}
		}

		return $label;
	}

	/**
	 * Get the field data.
	 *
	 * @param string $selector Field selector.
	 * @param mixed $default Default value.
	 *
	 * @return mixed
	 */
	public function get_field_data( string $selector, mixed $default = null ): mixed {
		$this->preload_meta();

		$value = get_field( $selector );

		if ( ! is_null( $value ) ) {
			return $value;
		}

		$value = get_field( $selector, $this->get_acf_id() );

		if ( ! is_null( $value ) ) {
			return $value;
		}

		return $default;
	}

	/**
	 * Preload the meta data.
	 *
	 * @return void
	 */
	private function preload_meta(): void {
		add_filter(
			'acf/pre_load_metadata',
			function ( $null, $post_id, $name, $hidden ) {
				$meta = $this->get_field_meta();
				$name = ( $hidden ? '_' : '' ) . $name;

				if ( isset( $meta[ $post_id ] ) ) {
					if ( isset( $meta[ $post_id ][ $name ] ) ) {
						return $meta[ $post_id ][ $name ];
					}
					return '__return_null';
				}

				return $null;
			},
			5,
			4
		);
	}

	/**
	 * Get the field meta.
	 *
	 * @return array
	 */
	private function get_field_meta(): array {
		return [
			$this->get_acf_id() => $this->block['data'] ?? [],
		];
	}

	/**
	 * Check if the field is required.
	 *
	 * @return bool
	 */
	public function is_required(): bool {
		return boolval( $this->get_field_data( 'required', false ) );
	}

	/**
	 * Get the required marker placement.
	 *
	 * @return string
	 */
	public function get_marker_placement(): string {
		return $this->req_marker_placement;
	}

	/**
	 * Get the field placeholder.
	 *
	 * @return string
	 */
	public function get_placeholder(): string {
		return $this->get_field_data( 'placeholder', '' );
	}

	/**
	 * Get Conditional logic rules for field.
	 *
	 * @return ?array
	 */
	public function get_conditional_logic(): ?array {
		if ( ! in_array( $this->get_block_name( true ), Form::ALL_FIELD_TYPES, true ) ) {
			return null;
		}

		if ( ! $this->get_field_data( 'enable_conditional_logic' ) ) {
			return null;
		}

		$logic = $this->get_field_data( 'conditional_logic' );

		if ( empty( $logic ) || ! is_array( $logic ) ) {
			return null;
		}

		if ( 'fieldset' === $this->get_block_name() ) {
			$container = sprintf( '#%s', $this->get_id_attr() );
		} else {
			$container = sprintf( 'div.form-input:has(#%s)', $this->get_id_attr() );
		}

		if ( empty( $logic[0]['action'] ) ) {
			return null;
		}

		$action = $logic[0]['action'];
		$rules  = [
			'container' => $container,
			'action'    => $action,
			'logic'     => 'and',
			'rules'     => [],
		];

		foreach ( $logic as $item ) {
			$rules['rules'][] = [
				'name'     => $item['field'],
				'operator' => $item['condition'],
				'value'    => $item['value'] ?? '',
			];
		}

//		error_log( print_r( $rules, true ) );

		return $rules;
	}

	/**
	 * Get the block template.
	 *
	 * @return array
	 */
	public function get_template(): array {
		return [];
	}

	/**
	 * Sanitize input from the field
	 *
	 * @param mixed $input
	 * @param ?Form $form
	 *
	 * @return string|array
	 */
	public function sanitize_input( mixed $input = null, ?Form $form = null ): string|array {
		if ( is_null( $input ) ) {
			if ( ! isset( $_REQUEST[ $this->get_name() ] ) ) {
				return '';
			}

			$input = ! is_array( $_REQUEST[ $this->get_name() ] ) ? trim( $_REQUEST[ $this->get_name() ] ) : array_filter( $_REQUEST[ $this->get_name() ] );
		}

		if ( empty( $input ) && '0' !== $input ) {
			return '';
		}

		if ( is_array( $input ) ) {
			return array_map(
				fn( $item ) => $this->sanitize_input( $item, $form ),
				$input
			);
		}

		if ( 'textarea' === $this->get_block_name() ) {
			$input = sanitize_textarea_field( $input );
			return str_replace( [ "\n", "\r", "'" ], [ "\\n", "\\r", "\'" ], $input );
		}

		return sanitize_text_field( $input );
	}

	/**
	 * Render the field value
	 *
	 * @param mixed $value
	 * @param Form  $form
	 *
	 * @return void
	 */
	public function render_value( mixed $value, Form $form ): void {
		if ( empty( $value ) ) {
			echo '<div class="text-input">&nbsp;</div>';
			return;
		}

		if ( is_array( $value ) ) {
			printf(
				'<pre class="text-input">%s</pre>',
				print_r( $value, true )
			);
		} elseif ( 'textarea' === $this->get_block_name() ) {
			printf(
				'<div class="text-input">%s</div>',
				nl2br( esc_textarea( stripslashes( $value ) ) )
			);
		} else {
			printf(
				'<div class="text-input">%s</div>',
				esc_html( $value )
			);
		}
	}

	/**
	 * Get the parent field ID.
	 *
	 * @return ?string
	 */
	public function get_parent_id(): ?string {
		return $this->parent_id;
	}

	/**
	 * Get the pattern ID.
	 *
	 * @return ?int
	 */
	public function get_pattern_id(): ?int {
		return $this->pattern_id;
	}
}
