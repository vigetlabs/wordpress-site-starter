<?php
/**
 * Options Trait
 *
 * @package VigetFormBlocks
 */

namespace VigetFormBlocks\Traits;

/**
 * Trait for Options
 */
trait Options {

	/**
	 * Get the select options.
	 *
	 * @return array
	 */
	public function get_options(): array {
		$opts    = $this->get_field_data( 'options' );
		$options = [];

		if ( ! $opts ) {
			return $options;
		}

		foreach ( $opts as $option ) {
			if ( ! $option['label'] && ! $option['value'] ) {
				continue;
			}

			$value = $option['custom'] ? $option['value'] : $option['label'];

			if ( $option['default'] && ! $this->get_default_value() ) {
				$this->set_default_value( $value );
			}

			$options[] = [
				'label' => $option['label'],
				'value' => $value,
			];
		}

		return $options;
	}
}
