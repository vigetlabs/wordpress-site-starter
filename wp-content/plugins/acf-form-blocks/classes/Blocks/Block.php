<?php
/**
 * Block Helper Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Blocks;

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
	private function filter_attrs(): void {
		add_filter(
			'acfbt_block_attrs',
			function ( array $attrs, array $block ): array {
				if ( ! in_array( $block['name'], $this->block_names, true ) ) {
					return $attrs;
				}

				$this->block = $block;
				$this->form = acffb_get_form( $this->block );

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