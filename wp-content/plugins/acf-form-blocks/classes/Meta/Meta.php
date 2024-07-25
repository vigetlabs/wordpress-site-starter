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
	 * Booleans of child meta values.
	 *
	 * @var array
	 */
	public array $booleans = [];

	/**
	 * If Meta Value is Boolean
	 *
	 * @var bool
	 */
	public bool $is_boolean = false;

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
	public function get_value( ?string $child = null, bool $for_display = false ): mixed {
		if ( is_null( $this->value ) ) {
			$this->set_value(); // Init the built-in value.
		}

		if ( $for_display ) {
			$this->convert_booleans();
		}

		if ( ! is_null( $child ) && is_array( $this->value ) ) {
			return $this->value[ $child ] ?? null;
		}

		return $this->value;
	}

	/**
	 * Convert boolean values to be readable
	 *
	 * @return void
	 */
	protected function convert_booleans(): void {
		if ( is_array( $this->value ) ) {
			$bools = $this->get_booleans();
			foreach ( $this->value as $key => &$value ) {
				if ( in_array( $key, $bools, true ) ) {
					$value = $value ? __( 'Yes', 'acf-form-blocks' ) : __( 'No', 'acf-form-blocks' );
				}
			}
		} elseif ( $this->is_boolean() ) {
			$this->value = $this->value ? __( 'Yes', 'acf-form-blocks' ) : __( 'No', 'acf-form-blocks' );
		}
	}

	private function get_booleans(): array {
		return $this->booleans;
	}

	private function is_boolean(): bool {
		return $this->is_boolean;
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
