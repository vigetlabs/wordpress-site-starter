<?php
/**
 * Radios Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Elements;

use ACFFormBlocks\Form;
use ACFFormBlocks\Traits\Options;
use ACFFormBlocks\Utilities\BlockTemplate\Block;
use ACFFormBlocks\Utilities\BlockTemplate\Template;
use Exception;

/**
 * Class for Radios Fields
 */
class Radios extends Field {
	use Options;

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
	 * Get the radio field wrapper attributes.
	 *
	 * @return array
	 */
	public function get_attrs(): array {
		$attrs = parent::get_attrs();

		// Discard name since this is on a div element.
		unset( $attrs['name'] );

		return $attrs;
	}

	/**
	 * Render the input value.
	 *
	 * @param mixed $value Value to render.
	 * @param Form  $form  Form object.
	 *
	 * @return void
	 */
	public function render_value( mixed $value, Form $form ): void {
		$options = $this->get_options();
		if ( ! $value || ! $options ) {
			parent::render_value( null, $form );
			return;
		}

		echo '<div class="text-input"><ul class="acffb-radio-list">';
		foreach ( $options as $option ) {
			printf(
				'<li%s><span class="radio"></span>%s</li>',
				$option['value'] === $value ? ' class="checked"' : '',
				esc_html( $option['value'] )
			);
		}
		echo '</ul></div>';
	}
}
