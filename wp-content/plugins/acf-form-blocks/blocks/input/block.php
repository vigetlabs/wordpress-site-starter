<?php
/**
 * Input block
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Blocks;

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Elements\Input;
use ACFFormBlocks\Elements\Number;

/**
 * Input Block Class
 */
class InputBlock extends FieldBlock {

	/**
	 * Type hint for the field object.
	 * @var ?Input
	 */
	protected ?Field $field;

	/**
	 * Set the block attributes.
	 *
	 * @param array $attrs The block attributes.
	 *
	 * @return array
	 */
	public function set_attrs( array $attrs ): array {
		$attrs = parent::set_attrs( $attrs );

		$attrs['type']  = $this->field->get_input_type();
		$attrs['value'] = $this->field->get_value();

		if ( $this->field->get_maxlength() ) {
			$attrs['maxlength'] = $this->field->get_maxlength();

			if ( 'number' === $this->field->get_input_type() ) {
				$js_maxlength     = 'if(this.value.length>this.maxLength)this.value=this.value.slice(0,this.maxLength)';
				$attrs['oninput'] = $js_maxlength;
				$attrs['onfocus'] = $js_maxlength;
			}
		}

		if ( 'number' === $this->field->get_input_type() ) {
			/** @var Number $this__field (TODO) */
			if ( ! $this->field->controls_enabled() ) {
				$attrs['data-appearance'] = 'none';
			} else {
				$attrs['step'] = $this->field->get_step();

				if ( $this->field->get_min() ) {
					$attrs['min'] = $this->field->get_min();
				}
				if ( $this->field->get_max() ) {
					$attrs['max'] = $this->field->get_max();
				}
			}
		}

		return $attrs;
	}
}

// Init block actions and filters.
( new InputBlock( 'acf/input' ) );
