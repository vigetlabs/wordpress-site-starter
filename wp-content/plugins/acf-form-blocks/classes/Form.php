<?php
/**
 * Form Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks;

/**
 * Class for Forms
 */
class Form {

	/**
	 * Hidden form ID.
	 *
	 * @var string
	 */
	const HIDDEN_FORM_ID = 'acffb_form_id';

	/**
	 * Block data.
	 *
	 * @var array
	 */
	protected array $block;

	/**
	 * @var ?Validation
	 */
	private ?Validation $validation = null;

	/**
	 * @var ?Submission
	 */
	private ?Submission $submission = null;

	/**
	 * @var ?Confirmation
	 */
	private ?Confirmation $confirmation = null;

	/**
	 * Constructor.
	 *
	 * @param array $block Block data.
	 */
	public function __construct( array $block, bool $preload_meta = false ) {
		$this->block = $block;

		if ( $preload_meta ) {
			$this->preload_meta();
		}
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
		$meta = [];

		$fields = $this->get_fields();
		foreach ( $fields as $field ) {
			$field_block = $field->get_block();
			if ( empty( $field_block['data'] ) ) {
				continue;
			}

			foreach ( $field_block['data'] as $key => $value ) {
				$meta[ $field->get_name() ][ $key ] = $value;
			}
		}

		return $meta;
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
	 * Update the cache.
	 *
	 * @return void
	 */
	public function update_cache(): void {
		Cache::set( get_block_id( $this->block ), $this );
	}

	/**
	 * Get the form action.
	 *
	 * @return string
	 */
	public function get_method(): string {
		return get_field( 'method' ) ?: 'post';
	}

	/**
	 * Get the form confirmation.
	 *
	 * @return Confirmation
	 */
	public function get_confirmation(): Confirmation {
		if ( null === $this->confirmation ) {
			$this->confirmation = new Confirmation( $this );
			$this->update_cache();
		}

		return $this->confirmation;
	}

	/**
	 * Get the form submission.
	 *
	 * @return Submission
	 */
	public function get_submission(): Submission {
		if ( null === $this->submission ) {
			$this->submission = new Submission( $this );
			$this->update_cache();
		}

		return $this->submission;
	}

	/**
	 * Get the form validation.
	 *
	 * @return Validation
	 */
	public function get_validation(): Validation {
		if ( null === $this->validation ) {
			$this->validation = new Validation( $this );
			$this->update_cache();
		}

		return $this->validation;
	}

	/**
	 * Get the fields from the form.
	 *
	 * @return array
	 */
	public function get_fields(): array {
		$content = get_the_content();
		$context = [ 'postId' => get_the_ID(), 'postType' => get_post_type() ];
		$blocks  = parse_blocks( $content );

		$field_blocks = $this->extract_field_blocks( $blocks, $context );

		return $this->parse_fields( $field_blocks );
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
		$types  = [ 'acf/input', 'acf/textarea' ];

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
			$fields[] = new Field( $field_block );
		}

		return $fields;
	}
}
