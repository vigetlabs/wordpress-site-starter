<?php
/**
 * Form Meta Class for Notifications
 *
 * @package VigetFormBlocks
 */

namespace VigetFormBlocks\Meta;

/**
 * Class for Notifications Meta
 */
class Notifications extends Meta {

	/**
	 * Notifications Meta Constructor
	 *
	 * @param ?string $form_id
	 */
	public function __construct( ?string $form_id = null ) {
		parent::__construct( $form_id );

		$this->key   = '_notifications';
		$this->type  = 'array';
		$this->label = __( 'Notifications', 'viget-form-blocks' );

		$this->children = [
			'admin'                 => [
				'type'  => 'bool',
				'label' => __( 'Admin Notification', 'viget-form-blocks' ),
			],
			'admin_template'        => [
				'type'  => 'post_id',
				'label' => __( 'Admin Template', 'viget-form-blocks' ),
			],
			'confirmation'          => [
				'type'  => 'bool',
				'label' => __( 'Confirmation Notification', 'viget-form-blocks' ),
			],
			'confirmation_template' => [
				'type'  => 'post_id',
				'label' => __( 'Confirmation Template', 'viget-form-blocks' ),
			],
			'custom'                => [
				'type'  => 'bool',
				'label' => __( 'Custom Notification', 'viget-form-blocks' ),
			],
			'recipient'             => [
				'type'  => 'string',
				'label' => __( 'Custom Email Recipient', 'viget-form-blocks' ),
			],
			'custom_template'       => [
				'type'  => 'post_id',
				'label' => __( 'Custom Template', 'viget-form-blocks' ),
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
			'admin'                 => $this->get_form()->get_notification()->is_admin_email_enabled(),
			'admin_template'        => $this->get_form()->get_notification()->get_admin_template(),
			'confirmation'          => $this->get_form()->get_notification()->is_confirmation_email_enabled(),
			'confirmation_template' => $this->get_form()->get_notification()->get_confirmation_template(),
			'custom'                => $this->get_form()->get_notification()->is_custom_email_enabled(),
			'recipient'             => $this->get_form()->get_notification()->get_custom_email(),
			'custom_template'       => $this->get_form()->get_notification()->get_custom_template(),
		];
	}
}
