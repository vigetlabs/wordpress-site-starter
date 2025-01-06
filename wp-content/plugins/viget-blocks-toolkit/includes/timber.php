<?php
/**
 * Timber Integration
 *
 * @package VigetBlocksToolkit
 */

add_action(
	'after_setup_theme',
	function () {
		if ( ! class_exists( 'Timber\Timber' ) ) {
			return;
		}

		add_filter(
			'timber/twig/functions',
			function ( array $functions ): array {
				$functions['inner_blocks'] = [
					'callable' => 'inner_blocks',
				];
				$functions['block_attrs'] = [
					'callable' => 'block_attrs',
				];

				return $functions;
			}
		);

	}
);
