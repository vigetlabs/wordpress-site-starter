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

	private ?Validation $validation = null;

	private ?Submission $submission = null;

	private ?Confirmation $confirmation = null;

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
