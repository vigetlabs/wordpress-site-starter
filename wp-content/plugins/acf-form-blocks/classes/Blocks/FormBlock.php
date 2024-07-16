<?php
/**
 * Form Block Helper Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Blocks;

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Utilities\Cache;

/**
 * Form Block
 */
class FormBlock extends Block {

	/**
	 * RadiosBlock constructor.
	 *
	 * @param string|array $block_names The block name.
	 */
	public function __construct( string|array $block_names = 'acf/form' ) {
		parent::__construct( $block_names );

		$this->filter_form_attrs();
	}

	/**
	 * Filter the form block attributes.
	 *
	 * @return void
	 */
	protected function filter_form_attrs(): void {
		add_filter(
			'acfbt_block_attrs',
			function ( array $attrs, array $block ): array {
				if ( ! in_array( $block['name'], $this->block_names, true ) ) {
					return $attrs;
				}

				$this->block = $block;

				if ( ! empty( $this->block['block_id'] ) ) {
					$this->form = Cache::get( $this->block['block_id'] );
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
	 * Set the block attributes.
	 *
	 * @param array $attrs The block attributes.
	 *
	 * @return array
	 */
	public function set_attrs( array $attrs ): array {
		$attrs['method'] = $this->form->get_form_object()->get_method();
		$attrs['action'] = '#' . $this->form->get_form_object()->get_id_attr();
		$attrs['id']     = $this->form->get_form_object()->get_id_attr();

		if ( $this->form->get_form_object()->has_field_type( 'input', 'file' ) ) {
			$attrs['enctype'] = 'multipart/form-data';
		}
		return $attrs;
	}

	/**
	 * Perform Template Redirect Actions
	 *
	 * @return void
	 */
	public function do_template_redirect(): void {
		// Handle form submission and redirects.
		$this->form->get_submission()->process();
	}
}
