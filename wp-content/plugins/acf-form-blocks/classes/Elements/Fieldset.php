<?php
/**
 * Fieldset Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Elements;

use ACFFormBlocks\Form;
use ACFFormBlocks\Traits\ChildFields;
use ACFFormBlocks\Utilities\BlockTemplate\Block;
use ACFFormBlocks\Utilities\BlockTemplate\Template;
use Exception;

/**
 * Class for Fieldsets
 */
class Fieldset extends Field {
	use ChildFields;

	/**
	 * Render the input value.
	 *
	 * @param mixed $value Value to render.
	 * @param Form  $form  Form object.
	 *
	 * @return void
	 */
	public function render_value( mixed $value, Form $form ): void {
		$children = $this->get_child_fields( $form );

		if ( empty( $children ) ) {
			parent::render_value( $value, $form );
			return;
		}

		if ( $this->is_checkbox_group() ) {
			$value = empty( $value ) ? [] : $value;

			echo '<div class="text-input"><ul class="acffb-checkbox-list">';

			/** @var Checkbox[] $children */
			foreach ( $children as $child ) {
				$checked   = in_array( $child->get_value_attr(), $value, true );
				$label     = $child->get_value_attr();
				$cbx_label = '';

				if ( $label !== $child->get_label() ) {
					$cbx_label = sprintf( ' <span class="cbx-label">(%s)</span>', esc_html( $child->get_label() ) );
				}

				printf(
					'<li%s><span class="checkbox">%s</span>%s</li>',
					$checked ? ' class="checked"' : '',
					$checked ? '<span class="dashicons dashicons-saved"></span>' : '',
					esc_html( $label ) . $cbx_label
				);
			}

			echo '</ul></div>';
			return;
		}

		foreach ( $children as $child ) {
			echo '<div class="acffb-sub-field">';
			printf(
				'<p class="sub-field-label" title="%s">%s</p>',
				esc_attr( $child->get_id() ),
				esc_html( $child->get_label() )
			);

			if ( empty( $value ) ) {
				parent::render_value( $value, $form );
			} else {
				foreach ( $value as $child_id => $val ) {
					if ( $child_id !== $child->get_id() ) {
						continue;
					}

					$child->set_default_value( $val );

					$child->render_value( $val, $form );
					break;
				}
			}
			echo '</div>';
		}
	}

	/**
	 * Get the block template.
	 *
	 * @return array
	 * @throws Exception
	 */
	public function get_template(): array {
		return ( new Template() )
			->add( ( new Block( 'acf/legend' ) ) )
			->add( ( new Block( 'core/paragraph', [ 'placeholder' => __( 'Type / to add fields...', 'acf-field-blocks' ) ] ) ) )
			->get();
	}

	/**
	 * Whether the fieldset is a checkbox group.
	 *
	 * @return bool
	 */
	public function is_checkbox_group(): bool {
		return ! empty( $this->block['is_checkbox_group'] );
	}
}