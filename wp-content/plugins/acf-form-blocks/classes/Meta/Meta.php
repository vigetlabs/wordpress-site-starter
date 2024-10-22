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
	 * Type of Meta Value.
	 *
	 * @var string
	 */
	public string $type = 'string';

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
			return $this->children[ $child ]['label'] ?? 'Undefined';
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
			$this->set_display_values();
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
	protected function set_display_values(): void {
		if ( is_array( $this->value ) ) {
			foreach ( $this->value as $key => &$value ) {
				$value = $this->get_display_value( $this->get_type( $key ), $value );
			}
		} else {
			$this->value = $this->get_display_value( $this->get_type(), $this->value );
		}
	}

	/**
	 * Get Display Value based on type
	 *
	 * @param string $type
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	private function get_display_value( string $type, mixed $value ): mixed {
		if ( 'bool' === $type ) {
			return $value ? __( 'Yes', 'acf-form-blocks' ) : __( 'No', 'acf-form-blocks' );
		}

		if ( 'post_id' === $type && false !== get_post_status( $value ) ) {
			return sprintf(
				'<a href="%s">%s</a> (%s)',
				get_edit_post_link( $value ),
				get_the_title( $value ),
				esc_html( $value )
			);
		}

		return $value;
	}

	/**
	 * Get the Meta value type.
	 *
	 * @param ?string $child
	 *
	 * @return string
	 */
	public function get_type( ?string $child = null ): string {
		if ( ! is_null( $child ) && 'array' === $this->type ) {
			return $this->children[ $child ]['type'] ?? 'string';
		}

		return $this->type;
	}

	/**
	 * Check if meta value is a specific type
	 *
	 * @param string $type
	 *
	 * @return bool
	 */
	private function is_type( string $type ): bool {
		return $type === $this->type;
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
