<?php
/**
 * Form Element Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Elements;

use ACFFormBlocks\Form as FormObject;
use ACFFormBlocks\Utilities\Blocks;
use ACFFormBlocks\Utilities\BlockTemplate\Block;
use ACFFormBlocks\Utilities\BlockTemplate\Template;
use Exception;

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
	 * Get the ID attribute.
	 *
	 * @return string
	 */
	public function get_id_attr(): string {
		if ( ! empty( $this->block['anchor'] ) ) {
			return $this->block['anchor'];
		}

		if ( empty( $this->block['form_id'] ) ) {
			return get_block_id( $this->block, true );
		}

		$block_name = str_replace( '/', '_', $this->block['name'] );
		return $block_name . '_' . $this->block['form_id'];
	}

	/**
	 * Get the actual form ID (unique/constant).
	 *
	 * @return string
	 */
	public function get_id(): string {
		if ( empty( $this->block['form_id'] ) ) {
			return get_block_id( $this->block, true );
		}

		$block_name = str_replace( '/', '_', $this->block['name'] );
		return $block_name . '_' . $this->block['form_id'];
	}

	/**
	 * Get the block ACF ID
	 *
	 * @return string
	 */
	public function get_acf_id(): string {
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
		$all_fields   = $this->get_all_fields( $content, $context );
		$input_fields = [];

		foreach ( $all_fields as $field ) {
			if ( in_array( $field->get_block_name( true ), FormObject::ALL_INPUT_TYPES  ) ) {
				$input_fields[] = $field;
			}
		}

		return $input_fields;
	}

	/**
	 * Get all the fields from the page content.
	 *
	 * @param string $content
	 * @param array  $context
	 *
	 * @return array
	 */
	public function get_all_fields( string $content = '', array $context = [] ): array {
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
		$filtered = Blocks::get_blocks_by_type( $blocks, FormObject::ALL_FIELD_TYPES );

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

		$value = get_field( $selector, $this->get_acf_id() );

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

	/**
	 * Get the form template.
	 *
	 * @return array
	 * @throws Exception
	 */
	public function get_template(): array {
		return ( new Template() )
			->add( ( new Block( 'acf/input' ) ) )
			->add( ( new Block( 'acf/submit', [ 'lock' => [ 'move' => false, 'remove' => true ] ] ) )
		)->get();
	}

	/**
	 * Get a field by its ID.
	 *
	 * @param string $field_id
	 *
	 * @return ?Field
	 */
	public function get_field_by_id( string $field_id ): ?Field {
		$fields = $this->get_all_fields();

		foreach ( $fields as $field ) {
			$block_name = str_replace( '/', '_', $field->get_block_name( true ) );
			$block_id   = $block_name . '_' . $field_id;

			if ( $field->get_id() === $block_id ) {
				return $field;
			}
		}

		return null;
	}
}
