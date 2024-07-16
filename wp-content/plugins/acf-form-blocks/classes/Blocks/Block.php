<?php
/**
 * Block Helper Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Blocks;

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Form;
use ACFFormBlocks\Utilities\Cache;

/**
 * Class for Block Actions
 */
class Block {

	/**
	 * The block name(s).
	 *
	 * @var array
	 */
	protected array $block_names;

	/**
	 * The field object.
	 *
	 * @var ?Field
	 */
	protected ?Field $field = null;

	/**
	 * The block array.
	 *
	 * @var array
	 */
	protected array $block = [];

	/**
	 * The WordPress block array.
	 *
	 * @var array
	 */
	protected array $wp_block = [];

	/**
	 * The form object.
	 *
	 * @var ?Form
	 */
	protected ?Form $form = null;

	/**
	 * Block constructor.
	 *
	 * @param string|array $block_names The block name.
	 */
	public function __construct( string|array $block_names ) {
		if ( ! is_array( $block_names ) ) {
			$block_names = [ $block_names ];
		}

		$this->block_names = $block_names;

		// Filter the block attributes.
		$this->filter_attrs();

		// Template redirect actions.
		$this->template_redirect();

		// Filter the block during render
		$this->render_block();
	}

	/**
	 * Filter the block attributes.
	 *
	 * @return void
	 */
	protected function filter_attrs(): void {
		add_filter(
			'acffb_block_attrs',
			function ( array $attrs, Field $field ): array {
				if ( ! in_array( $field->get_block_name( true ), $this->block_names, true ) ) {
					return $attrs;
				}

				$this->field = $field;
				$this->block = $this->field->get_block();

				if ( $this->field->get_context( 'acffb/form_id' ) ) {
					$this->form = Cache::get( $this->field->get_context( 'acffb/form_id' ) );
				} else {
					$this->form = acffb_get_form();
				}

				// Skip if we don't have a form.
				if ( ! $this->form ) {
					return $attrs;
				}

				return $this->set_attrs( $attrs );
			},
			10,
			2
		);
	}

	/**
	 * Template redirect.
	 *
	 * @return void
	 */
	private function template_redirect(): void {
		add_action(
			'template_redirect',
			function() {
				if ( ! $this->form ) {
					$this->form = acffb_get_form();

					// Skip if we don't have a form.
					if ( ! $this->form ) {
						return;
					}
				}

				$this->do_template_redirect();
			}
		);
	}

	/**
	 * Filter the block during render.
	 *
	 * @return void
	 */
	private function render_block(): void {
		add_filter(
			'render_block',
			function ( string $block_content, array $block ): string {
				if ( ! in_array( $block['blockName'], $this->block_names, true ) ) {
					return $block_content;
				}

				$this->wp_block = $block;

				return $this->render( $block_content );
			},
			10,
			2
		);
	}

	/**
	 * Set the block attributes.
	 *
	 * @param array $attrs The block attributes.
	 *
	 * @return array
	 */
	public function set_attrs( array $attrs ): array {
		return $attrs;
	}

	/**
	 * Do template redirect.
	 *
	 * @return void
	 */
	public function do_template_redirect(): void {
		// Do nothing.
	}

	/**
	 * Apply the render filter.
	 *
	 * @param string $block_content The block content.
	 *
	 * @return string
	 */
	public function render( string $block_content ): string {
		return $block_content;
	}
}
