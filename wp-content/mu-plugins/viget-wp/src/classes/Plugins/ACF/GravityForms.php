<?php
/**
 * GravityForms Class
 *
 * @package VigetWP
 */

namespace VigetWP\Plugins\ACF;

/**
 * GravityForms Class
 */
class GravityForms {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Insert Gravity Forms into ACF select field
		$this->populate_gravity_forms_ids();
	}

	/**
	 * Populate Gravity Forms IDs
	 *
	 * @return void
	 */
	private function populate_gravity_forms_ids(): void {
		add_filter(
			'acf/load_field/name=gravity_forms',
			function ( array $field ): array {

				if ( ! class_exists( 'GFFormsModel' ) ) {
					return $field;
				}

				$choices = [];

				foreach ( \GFFormsModel::get_forms() as $form ) {
					$choices[ $form->id ] = $form->title;
				}

				$field['choices'] = $choices;

				return $field;
			}
		);
	}
}
