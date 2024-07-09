<?php
/**
 * ACF Block Fields
 *
 * @package ACFFormBlocks
 */

add_action(
	'acf/include_fields',
	function() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group( array(
			'key' => 'group_6683facdcf97c',
			'title' => 'Block: Field',
			'fields' => array(
				array(
					'key' => 'field_6683facee1fbc',
					'label' => 'Placeholder',
					'name' => 'placeholder',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf/input',
					),
				),
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf/textarea',
					),
				),
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf/select',
					),
				),
			),
			'menu_order' => 6,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => true,
			'description' => '',
			'show_in_rest' => 0,
		) );

		acf_add_local_field_group( array(
			'key' => 'group_66832a3bbab06',
			'title' => 'Validation',
			'fields' => array(
				array(
					'key' => 'field_66832a3b582e4',
					'label' => 'Required',
					'name' => 'required',
					'aria-label' => '',
					'type' => 'true_false',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'message' => '',
					'default_value' => 0,
					'ui_on_text' => '',
					'ui_off_text' => '',
					'ui' => 1,
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf/input',
					),
				),
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf/textarea',
					),
				),
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf/radios',
					),
				),
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf/select',
					),
				),
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf/fieldset',
					),
				),
			),
			'menu_order' => 5,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => true,
			'description' => '',
			'show_in_rest' => 0,
		) );

		acf_add_local_field_group( array(
			'key' => 'group_668478504f2b5',
			'title' => 'Options',
			'fields' => array(
				array(
					'key' => 'field_66847850b53b2',
					'label' => 'Options',
					'name' => 'options',
					'aria-label' => '',
					'type' => 'repeater',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'layout' => 'block',
					'pagination' => 0,
					'min' => 0,
					'max' => 0,
					'collapsed' => '',
					'button_label' => 'Add Option',
					'rows_per_page' => 20,
					'sub_fields' => array(
						array(
							'key' => 'field_668478b1b53b6',
							'label' => 'Default',
							'name' => 'default',
							'aria-label' => '',
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'message' => '',
							'default_value' => 0,
							'ui' => 0,
							'ui_on_text' => '',
							'ui_off_text' => '',
							'parent_repeater' => 'field_66847850b53b2',
						),
						array(
							'key' => 'field_66847892b53b5',
							'label' => 'Custom',
							'name' => 'custom',
							'aria-label' => '',
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'message' => '',
							'default_value' => 0,
							'ui' => 0,
							'ui_on_text' => '',
							'ui_off_text' => '',
							'parent_repeater' => 'field_66847850b53b2',
						),
						array(
							'key' => 'field_66847869b53b3',
							'label' => 'Option',
							'name' => 'label',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'parent_repeater' => 'field_66847850b53b2',
						),
						array(
							'key' => 'field_66847882b53b4',
							'label' => 'Value',
							'name' => 'value',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => array(
								array(
									array(
										'field' => 'field_66847892b53b5',
										'operator' => '==',
										'value' => '1',
									),
								),
							),
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'parent_repeater' => 'field_66847850b53b2',
						),
					),
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf/select',
					),
				),
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf/radios',
					),
				),
			),
			'menu_order' => 10,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => true,
			'description' => '',
			'show_in_rest' => 0,
		) );

		acf_add_local_field_group( array(
			'key' => 'group_668c18f9290d7',
			'title' => 'Conditional Logic',
			'fields' => array(
				array(
					'key' => 'field_668c18f9beb53',
					'label' => 'Conditional Logic',
					'name' => '',
					'aria-label' => '',
					'type' => 'accordion',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'open' => 0,
					'multi_expand' => 1,
					'endpoint' => 0,
				),
				array(
					'key' => 'field_668c1944ef703',
					'label' => '',
					'name' => 'enable_conditional_logic',
					'aria-label' => '',
					'type' => 'true_false',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'message' => '',
					'default_value' => 0,
					'ui_on_text' => 'Enabled',
					'ui_off_text' => 'Disabled',
					'ui' => 1,
				),
				array(
					'key' => 'field_668c1a238cccd',
					'label' => 'Conditional Logic',
					'name' => 'conditional_logic',
					'aria-label' => '',
					'type' => 'repeater',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_668c1944ef703',
								'operator' => '==',
								'value' => '1',
							),
						),
					),
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'layout' => 'block',
					'pagination' => 0,
					'min' => 0,
					'max' => 0,
					'collapsed' => '',
					'button_label' => 'Add Condition',
					'rows_per_page' => 20,
					'sub_fields' => array(
						array(
							'key' => 'field_668c1a578ccce',
							'label' => 'Action',
							'name' => 'action',
							'aria-label' => '',
							'type' => 'select',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'choices' => array(
								'show' => 'Show',
								'hide' => 'Hide',
								'enable' => 'Enable',
								'disable' => 'Disable',
							),
							'default_value' => 'show',
							'return_format' => 'value',
							'multiple' => 0,
							'allow_null' => 0,
							'ui' => 0,
							'ajax' => 0,
							'placeholder' => '',
							'parent_repeater' => 'field_668c1a238cccd',
						),
						array(
							'key' => 'field_668c1b138ccd0',
							'label' => 'When this field',
							'name' => 'field',
							'aria-label' => '',
							'type' => 'select',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'choices' => array(
								'-' => 'N/A',
							),
							'default_value' => '-',
							'return_format' => 'value',
							'multiple' => 0,
							'allow_null' => 0,
							'ui' => 0,
							'ajax' => 0,
							'placeholder' => '',
							'parent_repeater' => 'field_668c1a238cccd',
						),
						array(
							'key' => 'field_668c1aaa8cccf',
							'label' => 'Meets this requirement',
							'name' => 'condition',
							'aria-label' => '',
							'type' => 'select',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'choices' => array(
								'isnotempty' => 'Has any value',
								'isempty' => 'Has no value',
								'is' => 'Has specific value',
								'isnot' => 'Does not have specific value',
								'greaterthan' => 'Is greater than',
								'lessthan' => 'Is less than',
								'contains' => 'Contains',
								'doesnotcontain' => 'Does not contain',
								'beginswith' => 'Begins With',
								'doesnotbeginwith' => 'Does not begin with',
							),
							'default_value' => 'not_empty',
							'return_format' => 'value',
							'multiple' => 0,
							'allow_null' => 0,
							'ui' => 0,
							'ajax' => 0,
							'placeholder' => '',
							'parent_repeater' => 'field_668c1a238cccd',
						),
						array(
							'key' => 'field_668c1bf3f5729',
							'label' => 'Value',
							'name' => 'value',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => array(
								array(
									array(
										'field' => 'field_668c1aaa8cccf',
										'operator' => '!=',
										'value' => 'isnotempty',
									),
									array(
										'field' => 'field_668c1aaa8cccf',
										'operator' => '!=',
										'value' => 'isempty',
									),
								),
							),
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'parent_repeater' => 'field_668c1a238cccd',
						),
					),
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf/input',
					),
				),
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf/radios',
					),
				),
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf/select',
					),
				),
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf/textarea',
					),
				),
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf/fieldset',
					),
				),
				array(
					array(
						'param' => 'block',
						'operator' => '==',
						'value' => 'acf/submit',
					),
				),
			),
			'menu_order' => 90,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => true,
			'description' => '',
			'show_in_rest' => 0,
		) );
	}
);
