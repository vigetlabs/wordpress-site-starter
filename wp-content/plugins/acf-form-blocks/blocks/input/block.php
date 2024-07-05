<?php
/**
 * Input block
 *
 * @package ACFFormBlocks
 */

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Elements\Input;
use ACFFormBlocks\Elements\Number;

add_filter(
	'acfbt_block_attrs',
	function ( array $attrs, array $block ): array {
		if ( 'acf/input' !== $block['name'] ) {
			return $attrs;
		}

		/** @var Input $input */
		$input = Field::factory( $block );

		$attrs['name']  = $input->get_name();
		$attrs['type']  = $input->get_input_type();
		$attrs['value'] = $input->get_value();

		if ( $input->get_maxlength() ) {
			$attrs['maxlength'] = $input->get_maxlength();

			if ( 'number' === $input->get_input_type() ) {
				$js_maxlength     = 'if(this.value.length>this.maxLength)this.value=this.value.slice(0,this.maxLength)';
				$attrs['oninput'] = $js_maxlength;
				$attrs['onfocus'] = $js_maxlength;
			}
		}

		if ( $input->get_placeholder() ) {
			$attrs['placeholder'] = $input->get_placeholder();
		}

		if ( $input->is_required() ) {
			$attrs['required'] = true;
		}

		if ( 'number' === $input->get_input_type() ) {
			/** @var Number $input */
			if ( ! $input->controls_enabled() ) {
				$attrs['data-appearance'] = 'none';
			} else {
				$attrs['step'] = $input->get_step();

				if ( $input->get_min() ) {
					$attrs['min'] = $input->get_min();
				}
				if ( $input->get_max() ) {
					$attrs['max'] = $input->get_max();
				}
			}
		}

		return $attrs;
	},
	10,
	2
);
