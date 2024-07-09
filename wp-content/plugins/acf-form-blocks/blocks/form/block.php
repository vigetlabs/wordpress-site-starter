<?php
/**
 * Form Block
 *
 * @package ACFFormBlocks
 */

add_filter(
	'acfbt_block_attrs',
	function ( array $attrs, array $block ): array {
		if ( 'acf/form' !== $block['name'] ) {
			return $attrs;
		}

		$form = acffb_get_form( $block );

		$attrs['method'] = $form->get_form_object()->get_method();
		$attrs['action'] = '#' . $form->get_form_object()->get_id_attr();
		$attrs['id']     = $form->get_form_object()->get_id_attr();

		if ( $form->get_form_object()->has_field_type( 'input', 'file' ) ) {
			$attrs['enctype'] = 'multipart/form-data';
		}

		return $attrs;
	},
	10,
	2
);

add_action(
	'template_redirect',
	function() {
		$form = acffb_get_form();
		if ( ! $form ) {
			return;
		}

		// Handle form submission and redirects.
		$form->get_submission()->process();
	}
);
