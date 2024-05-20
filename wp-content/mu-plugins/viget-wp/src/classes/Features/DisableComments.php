<?php
/**
 * Disable Comments
 *
 * @package VigetWP
 */

namespace VigetWP\Features;

/**
 * Disable Comments
 */
class DisableComments {

	/**
	 * DisableComments constructor.
	 */
	public function __construct() {
		// Disable comments.
		$this->disable_comments();

		// Remove Discussion Settings.
		$this->remove_discussion_settings();

		// Remove comments from admin bar.
		$this->remove_admin_bar_comments();

		// Remove REST API comments.
		$this->disable_rest_api_comments();
	}

	/**
	 * Disable comments.
	 *
	 * @return void
	 */
	private function disable_comments(): void {
		// Hide existing comments
		add_filter( 'comments_array', '__return_empty_array', 10, 2 );

		// Close comments on frontend.
		add_filter( 'comments_open', '__return_false', 20, 2 );
		add_filter( 'pings_open', '__return_false', 20, 2 );

		// Remove comments widget from dashboard
		add_action(
			'wp_dashboard_setup',
			function () {
				remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
			}
		);

		// Redirect any user trying to access comments page
		add_action(
			'admin_init',
			function () {
				global $pagenow;

				// Disable support for comments and trackbacks in post types
				foreach ( get_post_types() as $post_type ) {
					if ( post_type_supports( $post_type, 'comments' ) ) {
						remove_post_type_support( $post_type, 'comments' );
						remove_post_type_support( $post_type, 'trackbacks' );
					}
				}

				if ( 'edit-comments.php' === $pagenow ) {
					wp_redirect( admin_url() );
					exit;
				}
			}
		);
	}

	/**
	 * Remove the discussion settings.
	 *
	 * @return void
	 */
	private function remove_discussion_settings(): void {
		add_filter(
			'vigetwp_admin_menu',
			function ( array $menu ): array {
//				$menu[] = [
//					'menu'    => 'options-general.php',
//					'submenu' => 'options-discussion.php',
//					'remove'  => true,
//				];

				$menu[] = [
					'menu'   => 'edit-comments.php',
					'remove' => true,
				];

				return $menu;
			}
		);
	}

	/**
	 * Remove comments from the admin bar.
	 *
	 * @return void
	 */
	private function remove_admin_bar_comments(): void {
		add_filter(
			'vigetwp_admin_bar',
			function ( array $admin_bar ): array {
				$admin_bar[] = 'comments';
				return $admin_bar;
			}
		);

		add_action(
			'init',
			fn() => remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 )
		);
	}

	/**
	 * Disable Comments in the REST API
	 *
	 * @return void
	 */
	public function disable_rest_api_comments(): void {
		// Removes REST API endpoints
		add_filter( 'rest_pre_insert_comment', '__return_empty_string' );

		add_filter(
			'rest_endpoints',
			function ( array $endpoints ): array {
				unset( $endpoints['comments'] );
				return $endpoints;
			}
		);
	}
}
