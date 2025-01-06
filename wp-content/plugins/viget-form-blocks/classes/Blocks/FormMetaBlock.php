<?php
/**
 * Form Meta Block Helper Class
 *
 * @package VigetFormBlocks
 */

namespace VigetFormBlocks\Blocks;

use VigetFormBlocks\Views;

/**
 * Form Meta Block Class
 */
class FormMetaBlock extends Block {

	/**
	 * FormMetaBlock constructor.
	 *
	 * @param string|array $block_names The block name.
	 */
	public function __construct( string|array $block_names = 'acf/form-meta' ) {
		parent::__construct( $block_names );
	}

	/**
	 * Display the form meta value.
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
			'templates/form-meta/default',
			[
				'form'  => $this->get_form(),
				'meta'  => $data['meta'],
				'block' => $this,
			]
		);
	}
}
