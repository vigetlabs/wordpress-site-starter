<?php
/**
 * Options Trait
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Traits;

trait Options {

	/**
	 * Default value
	 *
	 * @var string
	 */
	private string $default_value = '';

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

			if ( $option['default'] && ! $this->default_value ) {
				$this->default_value = $value;
			}

			$options[] = [
				'label' => $option['label'],
				'value' => $value,
			];
		}

		return $options;
	}

	/**
	 * Get the field value.
	 *
	 * @return string|array
	 */
	public function get_value(): string|array {
		$value = parent::get_value();

		if ( ! $value ) {
			return $this->default_value;
		}

		return $value;
	}
}
