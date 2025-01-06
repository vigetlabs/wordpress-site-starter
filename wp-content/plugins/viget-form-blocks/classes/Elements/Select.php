<?php
/**
 * Select Class
 *
 * @package VigetFormBlocks
 */

namespace VigetFormBlocks\Elements;

use VigetFormBlocks\Form;
use VigetFormBlocks\Traits\Options;
use Exception;
use Viget\VigetBlocksToolkit\BlockTemplate\Block;
use Viget\VigetBlocksToolkit\BlockTemplate\Template;

/**
 * Class for Select Fields
 */
class Select extends Field {
	use Options;

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
	 * If multiple values are allowed.
	 *
	 * @return bool
	 */
	public function is_multiple(): bool {
		return boolval( $this->get_field_data( 'allow_multiple' ) );
	}

	/**
	 * Get the attributes for the select field.
	 *
	 * @return array
	 */
	public function get_attrs(): array {
		$attrs = parent::get_attrs();

		if ( is_admin() ) {
			$attrs['readonly'] = 'readonly';
		}

		if ( $this->is_multiple() ) {
			$attrs['multiple'] = 'multiple';
		}

		return $attrs;
	}

	/**
	 * Render the value.
	 *
	 * @param mixed $value The value.
	 * @param Form $form The form object.
	 *
	 * @return void
	 */
	public function render_value( mixed $value, Form $form ): void {
		if ( ! $this->is_multiple() || ! $value ) {
			parent::render_value( $value, $form );
			return;
		}

		$values = is_array( $value ) ? $value : [ $value ];

		printf(
			'<div class="text-input">%s</div>',
			implode( ', ', $values )
		);
	}

	/**
	 * Get the name attribute.
	 *
	 * @return string
	 */
	public function get_name_attr(): string {
		if ( $this->is_multiple() ) {
			return parent::get_name_attr() . '[]';
		}

		return parent::get_name_attr();
	}
}
