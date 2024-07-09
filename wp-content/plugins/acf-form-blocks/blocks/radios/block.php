<?php
/**
 * Radio buttons
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Blocks;

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Elements\Radios;

/**
 * Radios Block Class
 */
class RadiosBlock extends FieldBlock {

	/**
	 * Type hint for the field object.
	 * @var ?Radios
	 */
	protected ?Field $field;
}

// Init block actions and filters.
( new RadiosBlock( 'acf/radios' ) );
