<?php
/**
 * Form Element Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Elements;

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
	 * @param ?array $context
	 *
	 * @return array
	 */
	public function get_fields( string $content = '', ?array $context = null ): array {
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
	 *
	 * @return bool
	 */
	public function has_field_type( string $field_type ): bool {
		$fields = $this->get_fields();

		if ( ! str_starts_with( $field_type, 'acf/' ) ) {
			$field_type = 'acf/' . $field_type;
		}

		foreach ( $fields as $field ) {
			if ( $field->get_block()['name'] === $field_type ) {
				return true;
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
		$fields = [];
		$types  = [ 'acf/input', 'acf/textarea', 'acf/select' ];

		foreach( $blocks as $block ) {
			if ( in_array( $block['blockName'], $types ) ) {
				$attrs       = $block['attrs'] ?? [];
				$attrs['id'] = acf_get_block_id( $attrs, $context );
				$attrs['id'] = acf_ensure_block_id_prefix( $attrs['id'] );

				$attrs['wp_block'] = $block;

				$fields[] = $attrs;
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$fields = array_merge( $fields, $this->extract_field_blocks( $block['innerBlocks'], $context ) );
			}
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

		return get_field( $selector, get_block_id( $this->block ) );
	}
}
