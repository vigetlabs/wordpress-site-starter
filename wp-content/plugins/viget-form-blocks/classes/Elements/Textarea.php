<?php
/**
 * Textarea Class
 *
 * @package VigetFormBlocks
 */

namespace VigetFormBlocks\Elements;

use Exception;
use Viget\VigetBlocksToolkit\BlockTemplate\Block;
use Viget\VigetBlocksToolkit\BlockTemplate\Template;

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
		return ( new Template( new Block( 'acf/label' ) ) )->get();
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

	/**
	 * Get the Textarea Attributes.
	 *
	 * @return array
	 */
	public function get_attrs(): array {
		$attrs = parent::get_attrs();

		if ( is_admin() ) {
			$attrs['readonly'] = 'readonly';
		}

		return $attrs;
	}
}
