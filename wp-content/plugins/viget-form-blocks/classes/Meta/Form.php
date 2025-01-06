<?php
/**
 * Form Meta Class for Form
 *
 * @package VigetFormBlocks
 */

namespace VigetFormBlocks\Meta;

/**
 * Class for Form Meta
 */
class Form extends Meta {

	/**
	 * Form Meta Constructor
	 *
	 * @param ?string $form_id
	 */
	public function __construct( ?string $form_id = null ) {
		parent::__construct( $form_id );

		$this->key   = '_form';
		$this->type  = 'array';
		$this->label = __( 'Form', 'viget-form-blocks' );

		$this->children = [
			'id'      => [
				'type'  => 'string',
				'label' => __( 'Form ID', 'viget-form-blocks' ),
			],
			'name'    => [
				'type'  => 'string',
				'label' => __( 'Form Name', 'viget-form-blocks' ),
			],
			'markup'  => [
				'type'  => 'string',
				'label' => __( 'Form Markup', 'viget-form-blocks' ),
			],
			'context' => [
				'type'  => 'array',
				'label' => __( 'Form Context', 'viget-form-blocks' ),
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
			'id'      => $this->get_form()->get_form_object()->get_id(),
			'name'    => $this->get_form()->get_form_object()->get_name(),
			'markup'  => $this->get_form()->get_form_object()->get_form_markup(),
			'context' => $this->get_form()->get_form_object()->get_form_context(),
		];
	}
}
