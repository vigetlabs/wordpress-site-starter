<?php
/**
 * Form Meta Class for Request Method
 *
 * @package VigetFormBlocks
 */

namespace VigetFormBlocks\Meta;

use VigetFormBlocks\Form;

/**
 * Class for Request Method Meta
 */
class RequestMethod extends Meta {

	/**
	 * Request Method Meta Constructor
	 *
	 * @param ?string $form_id
	 */
	public function __construct( ?string $form_id = null ) {
		parent::__construct( $form_id );

		$this->key   = '_method';
		$this->label = __( 'Request Method', 'viget-form-blocks' );
	}

	/**
	 * Set Meta Value
	 *
	 * @param mixed $value
	 *
	 * @return void
	 */
	public function set_value( mixed $value = null ): void {
		if ( ! is_null( $value ) ) {
			parent::set_value( $value );
			return;
		}

		$this->value = $_SERVER['REQUEST_METHOD'] ?? '';
	}

	/**
	 * Sanitize the value
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	public function sanitize( mixed $value ): string {
		return sanitize_text_field( $value );
	}
}
