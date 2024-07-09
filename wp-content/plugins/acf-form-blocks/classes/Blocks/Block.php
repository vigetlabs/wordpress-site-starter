<?php
/**
 * Block Helper Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Blocks;

use ACFFormBlocks\Form;

class Block {

	/**
	 * The block name.
	 *
	 * @var string
	 */
	protected string $block_name;

	/**
	 * The block array.
	 *
	 * @var array
	 */
	protected array $block = [];

	/**
	 * The form object.
	 *
	 * @var ?Form
	 */
	protected ?Form $form = null;

	/**
	 * Block constructor.
	 *
	 * @param string $block_name The block name.
	 */
	public function __construct( string $block_name ) {
		$this->block_name = $block_name;

		$this->filter_attrs();
		$this->template_redirect();
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
				if ( $this->block_name !== $block['name'] ) {
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
}
