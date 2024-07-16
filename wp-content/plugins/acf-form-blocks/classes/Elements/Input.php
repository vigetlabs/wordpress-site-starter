<?php
/**
 * Input Field Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Elements;

use ACFFormBlocks\Form;
use ACFFormBlocks\Utilities\BlockTemplate\Block;
use ACFFormBlocks\Utilities\BlockTemplate\Template;
use Exception;

/**
 * Class for Input Fields
 */
class Input extends Field {

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
	 * Render the input value.
	 *
	 * @param mixed $value Value to render.
	 * @param Form $form Form object.
	 *
	 * @return void
	 */
	public function render_value( mixed $value, Form $form ): void {
		if ( 'file' !== $this->get_input_type() || empty( $value ) ) {
			parent::render_value( $value, $form );
			return;
		}

		printf(
			'<div class="text-input"><a href="%s" target="_blank" rel="noopener">%s</a></div>',
			esc_attr( $value['url'] ),
			esc_html__( 'View File', 'acf-form-blocks' )
		);
	}
}
