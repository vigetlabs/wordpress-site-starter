<?php
/**
 * Accordion Block Deprecation
 *
 * @package WPStarter
 */

/**
 * Mark accordion block as deprecated.
 *
 * Note: The actual migration is handled in JavaScript (deprecation.js).
 * The deprecation is registered via WordPress hooks in that file to ensure
 * the migration functions are available when needed.
 *
 * @param array $block_settings The block settings array.
 * @return array Modified block settings.
 */
add_filter(
	'acf/register_block_type_args',
	function ( $block_settings ) {
		// Only apply to the accordion block.
		if ( 'acf/accordion' !== $block_settings['name'] ) {
			return $block_settings;
		}

		// Add a note that this block is deprecated.
		if ( ! isset( $block_settings['description'] ) ) {
			$block_settings['description'] = '';
		}
		$block_settings['description'] .= ' ' . esc_html__(
			'This block is deprecated. Please use the core/accordion block instead.',
			'wp-starter'
		);

		return $block_settings;
	},
	10,
	1
);

/**
 * Enqueue JavaScript for block deprecation migration.
 * This handles the migration from acf/accordion to core/accordion.
 */
add_action(
	'enqueue_block_editor_assets',
	function () {
		$block_folder = '/blocks/accordion';
		$block_path   = get_template_directory() . $block_folder;
		$block_url    = get_template_directory_uri() . $block_folder;
		$js_path      = "$block_path/deprecation.js";

		if ( ! file_exists( $js_path ) ) {
			return;
		}

		wp_enqueue_script(
			'acf-accordion-deprecation',
			"$block_url/deprecation.js",
			[ 'wp-blocks', 'wp-dom-ready', 'wp-edit-post', 'wp-element' ],
			filemtime( $js_path ),
			true
		);
	},
	5 // Early priority to ensure it loads before block registration.
);

/**
 * Hide acf/accordion block from block inserter.
 * This prevents users from adding new acf/accordion blocks while allowing
 * existing blocks to continue working until they're migrated.
 */
add_filter(
	'allowed_block_types_all',
	function ( $allowed_block_types, $editor_context ) {
		// Remove acf/accordion from allowed blocks.
		if ( is_array( $allowed_block_types ) ) {
			$allowed_block_types = array_diff( $allowed_block_types, [ 'acf/accordion' ] );
		}

		return $allowed_block_types;
	},
	10,
	2
);

/**
 * Unregister block if WordPress version is 6.9+ and core/accordion is available.
 */
add_action(
	'init',
	function () {
		// Check if WordPress 6.9+ and core/accordion block is available.
		if ( version_compare( get_bloginfo( 'version' ), '6.9', '<' ) ) {
			return;
		}

		// Check if core/accordion block is registered.
		$block_registry = \WP_Block_Type_Registry::get_instance();
		if ( ! $block_registry->is_registered( 'core/accordion' ) ) {
			return;
		}

		// Unregister the ACF Accordion block.
		// Comment out the next line to allow existing blocks to continue working with deprecation notice.
		unregister_block_type( 'acf/accordion' );
	},
	11
);
