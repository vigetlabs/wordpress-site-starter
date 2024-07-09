<?php
/**
 * Select block
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Blocks;

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Elements\Select;

/**
 * Select Block Class
 */
class SelectBlock extends FieldBlock {

	/**
	 * Type hint for the field object.
	 * @var ?Select
	 */
	protected ?Field $field;
}

// Init block actions and filters.
new SelectBlock( 'acf/select' );
