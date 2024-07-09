<?php
/**
 * Form Block Helper Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Blocks;

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
