<?php
/**
 * Form Meta Class for URL
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Meta;

/**
 * Class for URL Meta
 */
class URL extends Meta {

	/**
	 * URL Meta Constructor
	 *
	 * @param ?string $form_id
	 */
	public function __construct( ?string $form_id = null ) {
		parent::__construct( $form_id );

		$this->key   = '_url';
		$this->label = __( 'Source URL', 'acf-form-blocks' );
	}

	/**
	 * Set Meta Value
	 *
	 * @param mixed $value
	 *
	 * @return void
	 */
	public function set_value( mixed $value = null ): void {
		$this->value = $_SERVER['REQUEST_URI'] ?? '';
	}

	/**
	 * Sanitize the value
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	public function sanitize( mixed $value ): string {
		return esc_url( $value );
	}
}
