<?php
/**
 * Block Template Creator
 *
 * @link https://developer.wordpress.org/block-editor/reference-guides/block-api/block-templates/
 *
 * @package ACFFormBlocks
 */

namespace Viget\VigetBlocksToolkit\BlockTemplate;

use Exception;

/**
 * Block Template
 */
class Template {

	/**
	 * Template
	 *
	 * @var array
	 */
	protected array $template = [];

	/**
	 * Constructor
	 *
	 * @param Block|array|null $block Template block.
	 *
	 * @return Template
	 * @throws Exception
	 */
	public function __construct( Block|array|null $block = null ) {
		if ( ! $block ) {
			return $this;
		}

		if ( is_array( $block ) ) {
			foreach ( $block as $item ) {
				$this->add( $item );
			}
		} else {
			$this->add( $block );
		}

		return $this;
	}

	/**
	 * Add a template block.
	 *
	 * @param Block|string $block Template block.
	 *
	 * @return Template
	 * @throws Exception
	 */
	public function add( Block|string $block ): Template {
		if ( is_string( $block ) ) {
			$block = new Block( $block );
		}

		$this->template[] = $block->get();
		return $this;
	}

	/**
	 * Get the template.
	 *
	 * @return array
	 */
	public function get(): array {
		return $this->template;
	}
}
