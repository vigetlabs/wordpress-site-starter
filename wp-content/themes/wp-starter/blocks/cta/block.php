<?php
/**
 * CTA Block
 *
 * @package WPStarter
 */

// Register Pattern Category.
add_action(
	'init',
	function () {
		register_block_pattern_category(
			'cta',
			[
				'label' => __( 'Call To Action', 'wp-starter' ),
			]
		);
	}
);
