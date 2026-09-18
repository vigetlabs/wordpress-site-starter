<?php
/**
 * FileEditors Class
 *
 * @package VigetWP
 */

namespace VigetWP\Admin;

/**
 * FileEditors Class
 */
class FileEditors {

	/**
	 * Disable the File Editors
	 */
	public function __construct() {
		// Disable Theme and Plugin Editors.
		$this->disable_file_editors();
	}

	/**
	 * Disable Theme and Plugin Editors
	 *
	 * @return void
	 */
	private function disable_file_editors(): void {
		// Disable Theme Editor.
		add_action(
			'admin_init',
			function () {
				if ( defined( 'DISALLOW_FILE_EDIT' ) ) {
					return;
				}

				if ( $this->should_allow_theme_file_edits() ) {
					return;
				}

				define( 'DISALLOW_FILE_EDIT', true );
			}
		);

		// When DISALLOW_FILE_EDIT is skipped, still hide WP's built-in file editors.
		add_action(
			'admin_menu',
			function () {
				if ( ! $this->should_allow_theme_file_edits() ) {
					return;
				}

				remove_submenu_page( 'themes.php', 'theme-editor.php' );
				remove_submenu_page( 'plugins.php', 'plugin-editor.php' );
			}
		);

		// Disable Plugin Editor.
		add_filter(
			'plugins_action_links',
			function ( array $links ): array {
				unset( $links['edit'] );
				return $links;
			}
		);
	}

	/**
	 * Whether theme file modifications should remain allowed.
	 *
	 * Create Block Theme 2.10+ hides its Site Editor sidebar when
	 * DISALLOW_FILE_EDIT is defined. Allow file edits only when that
	 * plugin is active on a local or development environment.
	 *
	 * @return bool
	 */
	private function should_allow_theme_file_edits(): bool {
		if ( ! in_array( wp_get_environment_type(), [ 'local', 'development' ], true ) ) {
			return false;
		}

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return is_plugin_active( 'create-block-theme/create-block-theme.php' );
	}
}
