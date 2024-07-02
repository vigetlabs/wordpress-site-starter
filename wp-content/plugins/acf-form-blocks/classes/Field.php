<?php
/**
 * Field Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks;

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
	 * Get the field input type.
	 *
	 * @return string
	 */
	public function get_input_type(): string {
		return $this->get_field_data( 'input_type' ) ?: 'text';
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
	 *
	 * @return mixed
	 */
	protected function get_field_data( string $selector ): mixed {
		return get_field( $selector, $this->block['id'] );
	}

	/**
	 * Check if the field is required.
	 *
	 * @return bool
	 */
	public function is_required(): bool {
		return boolval( $this->get_field_data( 'required' ) );
	}

	/**
	 * Get the field placeholder.
	 *
	 * @return string
	 */
	public function get_placeholder(): string {
		return $this->get_field_data( 'placeholder' ) ?? '';
	}
}
