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
			'menu_order' => 0,
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
						'value' => 'acf/select',
					),
				),
			),
			'menu_order' => 0,
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
