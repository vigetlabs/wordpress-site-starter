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

				define( 'DISALLOW_FILE_EDIT', true );
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
}
