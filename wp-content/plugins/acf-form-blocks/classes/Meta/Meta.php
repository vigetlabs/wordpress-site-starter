<?php
/**
 * Form Meta Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Meta;

use ACFFormBlocks\Form;

/**
 * Class for Form Meta
 */
class Meta {

	/**
	 * Registered Meta Fields
	 *
	 * @var Meta[]
	 */
	public static array $registered = [];

	/**
	 * The Meta Key
	 *
	 * @var string
	 */
	public string $key = '';

	/**
	 * The Meta Label
	 *
	 * @var string
	 */
	public string $label = 'Undefined';

	/**
	 * The Child Meta
	 *
	 * @var array
	 */
	public array $children = [];

	/**
	 * The Meta Value
	 *
	 * @var mixed
	 */
	public mixed $value = null;

	/**
	 * The Form ID
	 *
	 * @var ?string
	 */
	protected ?string $form_id;

	/**
	 * Meta Constructor
	 *
	 * @param ?string $form_id
	 */
	public function __construct( ?string $form_id = null ) {
		if ( $form_id ) {
			$this->form_id = $form_id;
		}
	}

	/**
	 * Get the form.
	 *
	 * @return ?Form
	 */
	public function get_form(): ?Form {
		if ( ! $this->form_id ) {
			return null;
		}

		return Form::get_instance( $this->form_id );
	}

	/**
	 * Get Meta Key
	 *
	 * @return string
	 */
	public function get_key(): string {
		return $this->key;
	}

	/**
	 * Get Meta Label
	 *
	 * @param ?string $child
	 *
	 * @return string
	 */
	public function get_label( ?string $child = null ): string {
		if ( ! is_null( $child ) ) {
			return $this->children[ $child ] ?? '';
		}

		return $this->label;
	}

	/**
	 * Set Meta Value
	 *
	 * @param mixed $value
	 *
	 * @return void
	 */
	public function set_value( mixed $value = null ): void {
		$this->value = $value;
	}

	/**
	 * Get Meta Value
	 *
	 * @param ?string $child
	 *
	 * @return mixed
	 */
	public function get_value( ?string $child = null ): mixed {
		if ( ! is_null( $child ) && is_array( $this->value ) ) {
			return $this->value[ $child ] ?? null;
		}

		return $this->value;
	}

	/**
	 * Get Sanitized Value
	 *
	 * @return mixed
	 */
	public function get_sanitized_value(): mixed {
		return $this->sanitize( $this->get_value() );
	}

	/**
	 * Sanitize the Value
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function sanitize( mixed $value ): mixed {
		return $value;
	}
}
