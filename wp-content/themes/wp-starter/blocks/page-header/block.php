<?php
/**
 * Page Header Block
 *
 * @package WPStarter
 */

// Register the Breadcrumbs Block Style.
add_action(
	'init',
	function () {
		register_block_style(
			'core/buttons',
			[
				'name'       => 'default',
				'label'      => __( 'Default', 'wp-starter' ),
				'is_default' => true,
			]
		);

		register_block_style(
			'core/buttons',
			[
				'name'  => 'breadcrumbs',
				'label' => __( 'Breadcrumbs', 'wp-starter' ),
			]
		);
	}
);
