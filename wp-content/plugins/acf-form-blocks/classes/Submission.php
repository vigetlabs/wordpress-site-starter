<?php
/**
 * Form Submission
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks;

/**
 * Submission Confirmation
 */
class Submission {

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
	 * Check if the form has been submitted.
	 *
	 * @return bool
	 */
	public function has_submit(): bool {
		return ! empty( $_REQUEST[ Form::HIDDEN_FORM_ID ] ) && get_block_id( $this->block ) === $_REQUEST[ Form::HIDDEN_FORM_ID ];
	}

	/**
	 * Check if the form submission was successful.
	 *
	 * @return bool
	 */
	public function is_success(): bool {
		if ( ! $this->has_submit() ) {
			return false;
		}

		if ( $this->has_errors() ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the form has errors.
	 *
	 * @return bool
	 */
	public function has_errors(): bool {
		$data = $this->get_data();
		return false;
	}

	/**
	 * Get the form data.
	 *
	 * @return array
	 */
	public function get_data(): array {
		$fields = $this->get_fields();
		$data   = [];
		return $data;
	}

	/**
	 * Get the fields from the form.
	 *
	 * @return array
	 */
	protected function get_fields(): array {
		$content = get_the_content();
		$blocks  = parse_blocks( $content );

		return $this->extract_fields( $blocks );
	}

	/**
	 * Extract fields from the blocks array.
	 *
	 * @param array $blocks
	 *
	 * @return array
	 */
	protected function extract_fields( array $blocks ): array {
		$fields = [];
		$types  = [ 'acf/input', 'acf/textarea' ];

		foreach( $blocks as $block ) {
			if ( in_array( $block['blockName'], $types ) ) {
				$fields[] = $block;
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$fields = array_merge( $fields, $this->extract_fields( $block['innerBlocks'] ) );
			}
		}

		return $fields;
	}
}
