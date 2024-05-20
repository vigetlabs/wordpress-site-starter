<?php
/**
 * Dashboard Widgets Class
 *
 * @package VigetWP
 */

namespace VigetWP\Admin;

/**
 * Dashboard Widgets Class
 */
class DashboardWidgets {

	/**
	 * Modify the dashboard widgets
	 */
	public function __construct() {
		// Remove some of the dashboard widgets.
		$this->remove_dashboard_widgets();
	}

	/**
	 * Remove some of the dashboard widgets.
	 *
	 * @return void
	 */
	private function remove_dashboard_widgets(): void {
		add_action(
			'wp_dashboard_setup',
			function () {
				remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
				remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
				remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
				remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
				remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
				remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
			}
		);
	}
}
