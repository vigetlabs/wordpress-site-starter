<?php
/**
 * Input Field Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Elements;

use ACFFormBlocks\Utilities\BlockTemplate\Block;
use ACFFormBlocks\Utilities\BlockTemplate\Template;
use Exception;

/**
 * Class for Input Fields
 */
class Input extends Field {

	/**
	 * Get the default value.
	 *
	 * @return mixed
	 */
	public function get_default_value(): mixed {
		$default = $this->get_field_data( 'default_value' );

		if ( ! $default ) {
			return parent::get_default_value();
		}

		return $default;
	}

	/**
	 * Get the field value.
	 *
	 * @return mixed
	 */
	public function get_value_attr(): mixed {
		return parent::get_value();
	}

	/**
	 * Get the field input type.
	 *
	 * @return string
	 */
	public function get_input_type(): string {
		return $this->get_field_data( 'type', 'text' );
	}

	/**
	 * Get the max length.
	 *
	 * @return ?int
	 */
	public function get_maxlength(): ?int {
		return $this->get_field_data( 'maxlength' ) ? intval( $this->get_field_data( 'maxlength' ) ) : null;
	}

	/**
	 * Get the block template.
	 *
	 * @return array
	 * @throws Exception
	 */
	public function get_template(): array {
		return (
			new Template(
				new Block(
					'core/paragraph',
					[ 'placeholder' => __( 'Field Label...', 'acf-form-blocks' ) ]
				)
			)
		)->get();
	}

	/**
	 * Get attributes for this field.
	 *
	 * @return array
	 */
	public function get_attrs(): array {
		$attrs = parent::get_attrs();

		$attrs['data-supports-jsx'] = null;

		$attrs['type']  = $this->get_input_type();
		$attrs['value'] = $this->get_value_attr();

		if ( is_admin() ) {
			$attrs['readonly'] = 'readonly';
		}

		if ( $this->get_maxlength() ) {
			$attrs['maxlength'] = $this->get_maxlength();

			if ( $this instanceof Number ) {
				$js_maxlength     = 'if(this.value.length>this.maxLength)this.value=this.value.slice(0,this.maxLength)';
				$attrs['oninput'] = $js_maxlength;
				$attrs['onfocus'] = $js_maxlength;
			}
		}

		if ( $this instanceof Number ) {
			if ( ! $this->controls_enabled() ) {
				$attrs['data-appearance'] = 'none';
			} else {
				$attrs['step'] = $this->get_step();

				if ( $this->get_min() ) {
					$attrs['min'] = $this->get_min();
				}
				if ( $this->get_max() ) {
					$attrs['max'] = $this->get_max();
				}
			}
		}

		return $attrs;
	}
}
