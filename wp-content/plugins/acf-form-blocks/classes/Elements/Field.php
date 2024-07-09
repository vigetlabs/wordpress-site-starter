<?php
/**
 * Field Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Elements;

use ACFFormBlocks\Form;

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
	 * Constructor.
	 *
	 * @param array $block Block data.
	 * @param array $context Block context.
	 */
	public function __construct( array $block, array $context = [] ) {
		$this->block   = $block;
		$this->context = $context;
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
	 * @param array $block Block data.
	 * @param array $context Context data.
	 *
	 * @return Field
	 */
	public static function factory( array $block, array $context = [] ): Field {
		$element = str_replace( 'acf/', '', $block['name'] );
		$class   = __NAMESPACE__ . '\\' . ucfirst( $element );

		if ( class_exists( $class ) ) {
			// Input handler.
			if ( 'input' === $element ) {
				$input = new $class( $block, $context );
				$type  = __NAMESPACE__ . '\\' . ucfirst( $input->get_input_type() );

				if ( class_exists( $type ) ) {
					return new $type( $block, $context );
				}
			}

			return new $class( $block, $context );
		}

		return new Field( $block );
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
		if ( empty( $this->block['field_id'] ) ) {
			return $this->get_acf_id();
		}

		$block_name = $this->get_block_name( true );
		$block_name = str_replace( '/', '_', $block_name );
		return $block_name . '_' . $this->block['field_id'];
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
	 * Get the field value.
	 *
	 * @return string
	 */
	public function get_value(): string {
		$value = $_REQUEST[ $this->get_name() ] ?? '';
		$value = sanitize_text_field( $value );
		return trim( $value );
	}

	/**
	 * Get the field label.
	 *
	 * @return string
	 */
	public function get_label(): string {
		if ( ! empty( $this->block['metadata']['name'] ) ) {
			return trim( $this->block['metadata']['name'] );
		}

		if ( empty( $this->block['wp_block']['innerBlocks'] ) ) {
			return '';
		}

		$label = '';
		foreach ( $this->block['wp_block']['innerBlocks'] as $inner_block ) {
			if ( 'core/paragraph' === $inner_block['blockName'] ) {
				$label = wp_strip_all_tags( $inner_block['innerHTML'] );
				break;
			}

			$label = wp_strip_all_tags( $inner_block['innerHTML'] );

			if ( $label ) {
				break;
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
}
