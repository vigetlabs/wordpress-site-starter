<?php
/**
 * Breadcrumbs Block
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

/**
 * Breadcrumbs Output
 *
 * @return void
 */
function wpstarter_breadcrumbs(): void {
	if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
		rank_math_the_breadcrumbs();
		return;
	}

	$block_template = [
		[
			'core/pattern',
			[
				'slug' => 'wp-starter/breadcrumbs-inner-blocks',
			],
		],
	];
	$allowed        = [
		'core/buttons',
		'core/button',
	];
	$inner          = [
		'template'      => $block_template,
		'allowedBlocks' => $allowed,
	];

	inner_blocks( $inner );
}
