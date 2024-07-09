<?php
/**
 * Radios Field Block Helper Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Blocks;

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Elements\Radios;

/**
 * Class for Field Block Actions
 */
class RadiosBlock extends Block {

	/**
	 * The field object.
	 *
	 * @var ?Radios
	 */
	protected ?Field $field = null;

	/**
	 * RadiosBlock constructor.
	 *
	 * @param string|array $block_names The block name.
	 */
	public function __construct( string|array $block_names = 'acf/radios' ) {
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
		if ( ! $this->field ) {
			$this->field = Field::factory( $this->block );
		}

		$attrs['id'] = $this->field->get_id_attr();

		return $attrs;
	}
}
