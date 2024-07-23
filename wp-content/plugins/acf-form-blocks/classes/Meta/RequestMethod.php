<?php
/**
 * Form Meta Class for Request Method
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Meta;

use ACFFormBlocks\Form;

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
		$this->label = __( 'Request Method', 'acf-form-blocks' );
	}

	/**
	 * Set Meta Value
	 *
	 * @param mixed $value
	 *
	 * @return void
	 */
	public function set_value( mixed $value = null ): void {
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
