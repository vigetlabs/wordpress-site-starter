<?php
/**
 * Block Helper Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Blocks;

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Form;

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
	 * The form object.
	 *
	 * @var ?Form
	 */
	protected ?Form $form = null;

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
	 * Block constructor.
	 *
	 * @param string|array $block_names The block name.
	 */
	public function __construct( string|array $block_names ) {
		if ( ! is_array( $block_names ) ) {
			$block_names = [ $block_names ];
		}

		$this->block_names = $block_names;

		// Template redirect actions.
		$this->template_redirect();

		// Filter the block during render
		$this->render_block();
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
					$this->form = Form::get_instance();

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
