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
	 * Constructor.
	 *
	 * @param array $block Block data.
	 */
	public function __construct( array $block ) {
		$this->block = $block;
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
		return new Confirmation( $this->block );
	}

	/**
	 * Get the form submission.
	 *
	 * @return Submission
	 */
	public function get_submission(): Submission {
		return new Submission( $this->block );
	}
}
