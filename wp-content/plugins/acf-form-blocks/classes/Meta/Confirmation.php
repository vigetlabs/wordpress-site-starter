<?php
/**
 * Form Meta Class for Confirmation
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Meta;

/**
 * Class for Confirmation Meta
 */
class Confirmation extends Meta {

	/**
	 * Confirmation Meta Constructor
	 *
	 * @param ?string $form_id
	 */
	public function __construct( ?string $form_id = null ) {
		parent::__construct( $form_id );

		$this->key   = '_confirmation';
		$this->type  = 'array';
		$this->label = __( 'Confirmation', 'acf-form-blocks' );

		$this->children = [
			'type' => [
				'type'  => 'string',
				'label' => __( 'Confirmation Type', 'acf-form-blocks' ),
			],
			'page' => [
				'type'  => 'post_id',
				'label' => __( 'Confirmation Page', 'acf-form-blocks' ),
			],
			'url'  => [
				'type'  => 'string',
				'label' => __( 'Redirect URL', 'acf-form-blocks' ),
			],
		];
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

		$this->value = [
			'type' => $this->get_form()->get_confirmation()->get_type(),
			'page' => $this->get_form()->get_form_object()->get_form_data( 'page' ),
			'url'  => $this->get_form()->get_form_object()->get_form_data( 'custom_url' ),
		];
	}
}