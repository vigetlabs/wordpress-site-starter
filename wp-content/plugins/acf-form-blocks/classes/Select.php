<?php
/**
 * Select Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks;

/**
 * Class for Select Fields
 */
class Select extends Field {

	/**
	 * Default value
	 *
	 * @var string
	 */
	private string $default_value = '';

	/**
	 * Get the field type.
	 *
	 * @return string
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
	 * @return string
	 */
	public function get_value(): string {
		$value = parent::get_value();
		if ( ! $value ) {
			return $this->default_value;
		}

		return $value;
	}
}
