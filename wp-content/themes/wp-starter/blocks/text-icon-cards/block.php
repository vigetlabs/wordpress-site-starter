<?php
/**
 * Text & Icon Cards Block
 *
 * @package WPStarter
 */

// Register Pattern Category.
add_action(
	'init',
	function () {
		register_block_pattern_category(
			'text-icon-cards',
			[
				'label' => __( 'Text & Icon Cards', 'wp-starter' ),
			]
		);
	}
);
