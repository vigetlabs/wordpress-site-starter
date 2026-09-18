<?php
/**
 * ACF Install Notice Class
 *
 * @package VigetWP
 */

namespace VigetWP\Plugins\ACF;

/**
 * ACF Install Notice Class
 */
class InstallNotice {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_notices', [ $this, 'render' ] );
	}

	/**
	 * Warn when ACF is missing, so a failed Composer install isn't silent.
	 *
	 * Local only - the fix is a local Composer install.
	 *
	 * @return void
	 */
	public function render(): void {
		if ( class_exists( 'ACF' ) || 'true' !== getenv( 'IS_DDEV_PROJECT' ) ) {
			return;
		}

		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		printf(
			'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
			esc_html__( 'ACF Pro is not installed.', 'viget-wp' ),
			esc_html__( 'Run "ddev composer-auth" to add the license key, then "ddev restart".', 'viget-wp' )
		);
	}
}
