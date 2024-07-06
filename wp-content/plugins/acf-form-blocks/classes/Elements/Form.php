<?php
/**
 * Form Element Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Elements;

use ACFFormBlocks\Utilities\Blocks;

/**
 * Class for Form Elements
 */
class Form {

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
	 * Get the form.
	 *
	 * @return array
	 */
	public function get_form(): array {
		return $this->block;
	}

	/**
	 * Get the form ID.
	 *
	 * @return string
	 */
	public function get_id(): string {
		return get_block_id( $this->block );
	}

	/**
	 * Get the form name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->get_form_meta( 'name' ) ?: get_the_title();
	}

	/**
	 * Get the form action.
	 *
	 * @return string
	 */
	public function get_method(): string {
		return $this->get_form_data( 'method' ) ?: 'post';
	}

	/**
	 * Get the fields from the page content.
	 *
	 * @param string $content
	 * @param array  $context
	 *
	 * @return array
	 */
	public function get_fields( string $content = '', array $context = [] ): array {
		if ( ! $content ) {
			$content = get_the_content();
		}

		if ( ! $context ) {
			$context = [ 'postId' => get_the_ID(), 'postType' => get_post_type() ];
		}

		$blocks = parse_blocks( $content );

		$field_blocks = $this->extract_field_blocks( $blocks, $context );

		return $this->parse_fields( $field_blocks );
	}

	/**
	 * Check if the form has a field of a specific type.
	 *
	 * @param string $field_type
	 * @param string $sub_type
	 *
	 * @return bool
	 */
	public function has_field_type( string $field_type, string $sub_type = '' ): bool {
		$fields = $this->get_fields();

		if ( ! str_starts_with( $field_type, 'acf/' ) ) {
			$field_type = 'acf/' . $field_type;
		}

		foreach ( $fields as $field ) {
			if ( $field->get_block_name( true ) === $field_type ) {
				if ( ! $sub_type || $sub_type === $field->get_type() ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Extract fields from the blocks array.
	 *
	 * @param array $blocks
	 * @param array $context
	 *
	 * @return array
	 */
	private function extract_field_blocks( array $blocks, array $context ): array {
		$fields   = [];
		$filtered = Blocks::get_blocks_by_type( $blocks, [ 'acf/input', 'acf/textarea', 'acf/select' ] );

		foreach ( $filtered as $block ) {
			$attrs       = $block['attrs'] ?? [];
			$attrs['id'] = acf_get_block_id( $attrs, $context );
			$attrs['id'] = acf_ensure_block_id_prefix( $attrs['id'] );

			$attrs['wp_block'] = $block;

			$fields[] = $attrs;
		}

		return $fields;
	}

	/**
	 * Parse the field blocks into Field objects.
	 *
	 * @param array $field_blocks
	 *
	 * @return Field[]
	 */
	private function parse_fields( array $field_blocks ): array {
		$fields = [];

		foreach ( $field_blocks as $field_block ) {
			// Warning: Using Field::factory here causes infinite loop.
			$fields[] = new Field( $field_block );
		}

		return $fields;
	}

	/**
	 * Get the form data.
	 *
	 * @param string $selector Field selector.
	 *
	 * @return mixed
	 */
	public function get_form_data( string $selector ): mixed {
		$value = get_field( $selector );

		if ( ! is_null( $value ) ) {
			return $value;
		}

		$value = get_field( $selector, $this->get_id() );

		if ( ! is_null( $value ) ) {
			return $value;
		}

		// Not sure why this is all of sudden necessary.
		if ( isset( $this->block['data'][ $selector ] ) ) {
			return $this->block['data'][ $selector ];
		}

		return null;
	}

	/**
	 * Get the form metadata.
	 *
	 * @param string $key Meta key.
	 *
	 * @return mixed
	 */
	public function get_form_meta( string $key ): mixed {
		if ( empty( $this->block['metadata'][ $key ] ) ) {
			return null;
		}

		return $this->block['metadata'][ $key ];
	}
}
