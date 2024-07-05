<?php
/**
 * Number Input Field Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Elements;

/**
 * Class for Number Input Fields
 */
class Number extends Input {

	/**
	 * Get the min value
	 *
	 * @return bool
	 */
	public function controls_enabled(): bool {
		return boolval( $this->get_field_data( 'controls' ) );
	}

	/**
	 * Get the min value
	 *
	 * @return ?int
	 */
	public function get_min(): ?int {
		return $this->get_field_data( 'min' ) ? intval( $this->get_field_data( 'min' ) ) : null;
	}

	/**
	 * Get the max value
	 *
	 * @return ?int
	 */
	public function get_max(): ?int {
		return $this->get_field_data( 'max' ) ? intval( $this->get_field_data( 'max' ) ) : null;
	}

	/**
	 * Get the step count
	 *
	 * @return int
	 */
	public function get_step(): int {
		return intval( $this->get_field_data( 'step', 1 ) );
	}
}
