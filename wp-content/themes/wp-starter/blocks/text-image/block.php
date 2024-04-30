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
