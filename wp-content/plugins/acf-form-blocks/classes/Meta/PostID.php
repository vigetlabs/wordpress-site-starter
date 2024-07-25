<?php
/**
 * Form Meta Class for Post ID
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Meta;

/**
 * Class for Post ID Meta
 */
class PostID extends Meta {

	/**
	 * Post ID Meta Constructor
	 *
	 * @param ?string $form_id
	 */
	public function __construct( ?string $form_id = null ) {
		parent::__construct( $form_id );

		$this->key   = '_post_id';
		$this->label = __( 'Post ID', 'acf-form-blocks' );
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

		$this->value = get_queried_object_id();
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
