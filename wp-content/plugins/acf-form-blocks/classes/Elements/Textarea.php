<?php
/**
 * Textarea Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Elements;

use ACFFormBlocks\Utilities\BlockTemplate\Block;
use ACFFormBlocks\Utilities\BlockTemplate\Template;
use Exception;

/**
 * Class for Textarea Fields
 */
class Textarea extends Field {

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
					[ 'placeholder' => __( 'Field Label...', 'acf-form-blocks' )
					]
				)
			)
		)->get();
	}

	/**
	 * Get the textarea value
	 *
	 * @return string
	 */
	public function get_value(): string {
		$value = parent::get_value() ?: '';
		return str_replace( '\r\n', "\r\n", $value );
	}
}
