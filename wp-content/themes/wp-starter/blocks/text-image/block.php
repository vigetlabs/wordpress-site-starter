<?php
/**
 * Text & Image Block
 *
 * @package WPStarter
 */

// Register Pattern Category.
add_action(
	'init',
	function () {
		register_block_pattern_category(
			'text-image',
			[
				'label' => __( 'Text & Image', 'wp-starter' ),
			]
		);
	}
);

// Register the Eyebrow Block Style.
add_action(
	'init',
	function () {
		register_block_style(
			'core/paragraph',
			[
				'name'       => 'default',
				'label'      => __( 'Default', 'wp-starter' ),
				'is_default' => true,
			]
		);

		register_block_style(
			'core/paragraph',
			[
				'name'  => 'eyebrow',
				'label' => __( 'Eyebrow', 'wp-starter' ),
			]
		);
	}
);
