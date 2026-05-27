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

		// Disable default comments in the REST API.
		$this->disable_rest_api_comments();

		// Remove comment blocks.
		$this->remove_comment_blocks();
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

				// Disable support for comments and trackbacks.
				foreach ( get_post_types() as $post_type ) {
					if ( post_type_supports( $post_type, 'comments' ) ) {
						remove_post_type_support( $post_type, 'comments' );
					}
					if ( post_type_supports( $post_type, 'trackbacks' ) ) {
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
		// Block only default/public comments while allowing editor Notes.
		add_filter(
			'rest_pre_insert_comment',
			function ( $prepared_comment, \WP_REST_Request $request ) {
				$comment_type = $request->get_param( 'type' )
					?? ( $prepared_comment->comment_type ?? null )
					?? 'comment';

				if ( 'note' === $comment_type ) {
					return $prepared_comment;
				}

				return new \WP_Error(
					'comments_disabled',
					__( 'Comments are disabled for this site.', 'viget-wp' ),
					[ 'status' => 403 ]
				);
			},
			10,
			2
		);
	}

	/**
	 * Remove comment blocks.
	 *
	 * @return void
	 */
	private function remove_comment_blocks(): void {
		add_filter(
			'allowed_block_types_all',
			function ( array|bool $allowed_block_types, \WP_Block_Editor_Context $context ): array|bool {
				if ( ! is_array( $allowed_block_types ) ) {
					$allowed_block_types = array_keys( \WP_Block_Type_Registry::get_instance()->get_all_registered() );
				}

				$removed_blocks = [
					'core/comment-template',
					'core/post-comment',
					'core/post-comments-count',
					'core/post-comments-form',
					'core/post-comments-link',
					'core/latest-comments',
					'core/comments',
					'core/comments-title',
					'core/comments-pagination',
					'core/comments-pagination-next',
					'core/comments-pagination-previous',
					'core/comments-pagination-numbers',
					'core/comment-author-name',
					'core/comment-author-avatar',
					'core/comment-content',
					'core/comment-date',
					'core/comment-edit-link',
					'core/comment-reply-link',
				];

				return array_values( array_diff( $allowed_block_types, $removed_blocks ) );
			},
			10,
			2
		);
	}
}
