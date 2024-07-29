<?php
/**
 * Form Data Block Helper Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Blocks;

use ACFFormBlocks\Views;

/**
 * Submit Block Class
 */
class FormDataBlock extends Block {

	/**
	 * FormDataBlock constructor.
	 *
	 * @param string|array $block_names The block name.
	 */
	public function __construct( string|array $block_names = 'acf/form-data' ) {
		parent::__construct( $block_names );
	}

	/**
	 * Display the form field value.
	 *
	 * @param string $block_content
	 *
	 * @return string
	 */
	public function render( string $block_content ): string {
		if ( ! $this->get_form() ) {
			return $block_content;
		}

		$data = $this->get_form()->get_submission()->get_data();
		return Views::get(
			'templates/form-data/default',
			[
				'form'    => $this->get_form(),
				'content' => $data['content'],
				'block'   => $this,
			]
		);
	}
}
