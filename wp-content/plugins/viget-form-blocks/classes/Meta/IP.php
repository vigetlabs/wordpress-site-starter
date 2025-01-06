<?php
/**
 * Form Meta Class for IP
 *
 * @package VigetFormBlocks
 */

namespace VigetFormBlocks\Meta;

/**
 * Class for IP Meta
 */
class IP extends Meta {

	/**
	 * IP Meta Constructor
	 *
	 * @param ?string $form_id
	 */
	public function __construct( ?string $form_id = null ) {
		parent::__construct( $form_id );

		$this->key   = '_ip';
		$this->label = __( 'IP Address', 'viget-form-blocks' );
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

		$this->value = $_SERVER['REMOTE_ADDR'] ?? '';
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
