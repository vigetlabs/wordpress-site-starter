<?php
/**
 * Field Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Elements;

use ACFFormBlocks\Form;
use ACFFormBlocks\Utilities\Cache;
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
	 * Get the form object.
	 *
	 * @return ?Form
	 */
	public function get_form(): ?Form {
		return Form::get_instance( $this->get_context( 'acffb/form_id' ) );
	}

	/**
	 * Get the fieldset.
	 *
	 * @return ?Field
	 */
	public function get_fieldset(): ?Field {
		if ( ! $this->get_context( 'acffb/fieldset_id' ) ) {
			return null;
		}

		$fieldset_id = 'acf_fieldset_' . $this->get_context( 'acffb/fieldset_id' );
		return $this->get_form()?->get_form_object()->get_field_by_id( $fieldset_id );
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
		if ( empty( $this->block['block_id'] ) ) {
			return $this->get_acf_id();
		}

		$block_name = $this->get_block_name( true );
		$block_name = str_replace( '/', '_', $block_name );
		return $block_name . '_' . $this->block['block_id'];
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
	 * @return string|array
	 */
	public function get_value(): string|array {
		$value = $_REQUEST[ $this->get_name() ] ?? $this->get_default_value();

		if ( is_array( $value ) ) {
			return array_map( 'sanitize_text_field', $value );
		}

		$value = sanitize_text_field( $value );
		return trim( $value );
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

			$label = wp_strip_all_tags( $inner_block['innerHTML'] );

			if ( $label ) {
				break;
			}

			if ( 'acf/legend' === $inner_block['blockName'] && ! empty( $inner_block['innerBlocks'] ) ) {
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
		$value = get_field( $selector );

		if ( ! is_null( $value ) ) {
			return $value;
		}

		$value = get_field( $selector, $this->block['id'] );

		if ( ! is_null( $value ) ) {
			return $value;
		}

		// Not sure why this is all of sudden necessary.
		if ( isset( $this->block['data'][ $selector ] ) ) {
			return $this->block['data'][ $selector ];
		}

		return $default;
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
	 *
	 * @return string|array|null
	 */
	public function sanitize_input( mixed $input ): string|array|null {
		if ( is_array( $input ) ) {
			return array_map( 'sanitize_text_field', $input );
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
}
