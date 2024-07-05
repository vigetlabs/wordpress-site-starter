<?php
/**
 * Field Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Elements;

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
	 * Constructor.
	 *
	 * @param array $block Block data.
	 */
	public function __construct( array $block ) {
		$this->block = $block;
	}

	/**
	 * Factory method to create a new field object.
	 *
	 * @param array $block Block data.
	 *
	 * @return Field
	 */
	public static function factory( array $block ): Field {
		$element = str_replace( 'acf/', '', $block['name'] );
		$class   = __NAMESPACE__ . '\\' . ucfirst( $element );

		if ( class_exists( $class ) ) {
			// Input handler.
			if ( 'input' === $element ) {
				$input = new $class( $block );
				$type  = __NAMESPACE__ . '\\' . ucfirst( $input->get_input_type() );

				if ( class_exists( $type ) ) {
					return new $type( $block );
				}
			}

			return new $class( $block );
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
	 * Get the field ID.
	 *
	 * @return string
	 */
	public function get_id(): string {
		return get_block_id( $this->block );
	}

	/**
	 * Get the field name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->block['id'];
	}

	/**
	 * Get the field type.
	 *
	 * @return string
	 */
	public function get_type(): string {
		return $this->block['blockName'];
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
	protected function get_field_data( string $selector, mixed $default = null ): mixed {
		$data = get_field( $selector, $this->block['id'] );

		if ( is_null( $data ) && ! is_null( $default ) ) {
			return $default;
		}

		return $data;
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
}
