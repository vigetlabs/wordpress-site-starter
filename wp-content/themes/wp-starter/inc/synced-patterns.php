<?php
/**
 * Bootstraps optional synced patterns (wp_block) shipped with the theme.
 *
 * @package WPStarter
 */

declare(strict_types=1);

/**
 * Bump this when the CTA inner markup changes so existing sites update their wp_block content.
 */
const WPSTARTER_SYNCED_CTA_INNER_VERSION = 1;

/**
 * Option key storing the last applied CTA inner pattern version.
 */
const WPSTARTER_SYNCED_CTA_INNER_OPTION = 'wp_starter_synced_cta_inner_version';

/**
 * Option key storing the wp_block post ID for the CTA inner pattern (avoids querying every admin request).
 */
const WPSTARTER_SYNCED_CTA_INNER_POST_ID_OPTION = 'wp_starter_synced_cta_inner_post_id';

/**
 * Post slug for the CTA inner synced pattern (wp_block post_name).
 */
const WPSTARTER_SYNCED_CTA_INNER_SLUG = 'wp-starter-cta-inner-synced';

/**
 * Returns block markup for the CTA inner synced pattern (must stay in sync with patterns/cta-inner-content-only.php).
 *
 * @return string
 */
function wpstarter_get_cta_inner_synced_pattern_content(): string {
	return trim(
		<<<'HTML'
<!-- wp:group {"templateLock":"contentOnly","metadata":{"name":"CTA inner (content-only)"},"layout":{"type":"constrained","justifyContent":"center"}} -->
<div class="wp-block-group">
	<!-- wp:heading {"textAlign":"center","level":2,"placeholder":"Headline Goes Here"} -->
	<h2 class="wp-block-heading has-text-align-center"></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center","placeholder":"Body text goes here. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."} -->
	<p class="has-text-align-center"></p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button"></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
HTML
	);
}

/**
 * Creates or updates the bundled CTA inner synced pattern.
 *
 * @param bool $force When true, always rewrite post_content from the theme (e.g. WP-CLI).
 * @return int|false wp_block post ID on success, false on failure.
 */
function wpstarter_bootstrap_cta_inner_synced_pattern( bool $force = false ) {
	if ( ! post_type_exists( 'wp_block' ) ) {
		return false;
	}

	$stored_version = (int) get_option( WPSTARTER_SYNCED_CTA_INNER_OPTION, 0 );
	$cached_id      = (int) get_option( WPSTARTER_SYNCED_CTA_INNER_POST_ID_OPTION, 0 );

	if ( ! $force && $stored_version >= WPSTARTER_SYNCED_CTA_INNER_VERSION && $cached_id > 0 ) {
		$cached = get_post( $cached_id );
		if ( $cached && 'wp_block' === $cached->post_type ) {
			return $cached_id;
		}
	}

	$needs_update = $force || $stored_version < WPSTARTER_SYNCED_CTA_INNER_VERSION;

	$existing = get_posts(
		array(
			'post_type'              => 'wp_block',
			'name'                   => WPSTARTER_SYNCED_CTA_INNER_SLUG,
			'post_status'            => array( 'publish', 'draft', 'private' ),
			'numberposts'            => 1,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
		)
	);

	$content = wpstarter_get_cta_inner_synced_pattern_content();

	if ( $existing ) {
		$post_id = (int) $existing[0]->ID;
		if ( ! $needs_update ) {
			update_option( WPSTARTER_SYNCED_CTA_INNER_POST_ID_OPTION, $post_id );

			return $post_id;
		}

		$result = wp_update_post(
			array(
				'ID'           => $post_id,
				'post_content' => $content,
				'post_status'  => 'publish',
			),
			true
		);

		if ( is_wp_error( $result ) ) {
			return false;
		}

		update_option( WPSTARTER_SYNCED_CTA_INNER_OPTION, WPSTARTER_SYNCED_CTA_INNER_VERSION );
		update_option( WPSTARTER_SYNCED_CTA_INNER_POST_ID_OPTION, $post_id );

		return $post_id;
	}

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'wp_block',
			'post_status'  => 'publish',
			'post_title'   => __( 'WP Starter: CTA inner (synced)', 'wp-starter' ),
			'post_name'    => WPSTARTER_SYNCED_CTA_INNER_SLUG,
			'post_content' => $content,
			'post_excerpt' => __( 'Synced inner layout for call-to-action sections. Update markup in the theme and bump WPSTARTER_SYNCED_CTA_INNER_VERSION to roll out changes.', 'wp-starter' ),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		return false;
	}

	update_option( WPSTARTER_SYNCED_CTA_INNER_OPTION, WPSTARTER_SYNCED_CTA_INNER_VERSION );
	update_option( WPSTARTER_SYNCED_CTA_INNER_POST_ID_OPTION, (int) $post_id );

	return (int) $post_id;
}

/**
 * Runs bootstrap when the theme is activated or when an admin session loads after a version bump.
 */
function wpstarter_maybe_bootstrap_synced_patterns(): void {
	if ( ! is_admin() || ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	wpstarter_bootstrap_cta_inner_synced_pattern( false );
}

add_action(
	'after_switch_theme',
	static function (): void {
		wpstarter_bootstrap_cta_inner_synced_pattern( false );
	}
);
add_action( 'admin_init', 'wpstarter_maybe_bootstrap_synced_patterns' );
