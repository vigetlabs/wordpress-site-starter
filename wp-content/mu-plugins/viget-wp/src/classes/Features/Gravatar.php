<?php
/**
 * Gravatar
 *
 * @package VigetWP
 */

namespace VigetWP\Features;

/**
 * Gravatar
 */
class Gravatar {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Fix Gravatar Avatars.
		$this->avatar_fix();

		// Disable Gravatar settings.
		$this->disable_settings();
	}

	/**
	 * Remove the + from email addresses to fix Gravatar image
	 *
	 * @return void
	 */
	private function avatar_fix(): void {
		add_filter(
			'pre_get_avatar_data',
			function ( array $args, mixed $id_or_email ): array {
				if ( is_numeric( $id_or_email ) ) {
					$user        = get_user_by( 'id', $id_or_email );
					$id_or_email = $user->user_email;
				}

				if ( ! is_string( $id_or_email ) ) {
					return $args;
				}

				if ( ! str_contains( $id_or_email, '@' ) || ! str_contains( $id_or_email, '+' ) ) {
					return $args;
				}

				global $pagenow;

				if ( 'options-discussion.php' === $pagenow ) {
					if ( did_action( 'wp_before_admin_bar_render' ) ) {
						return $args;
					}
				}

				$email       = preg_replace( '/\+[^)]*@/i', '@', $id_or_email );
				$email_hash  = md5( strtolower( trim( $email ) ) );
				$args['url'] = 'https://secure.gravatar.com/avatar/' . $email_hash;

				return $args;
			},
			10,
			2
		);
	}

	/**
	 * Rename Discussion settings
	 *
	 * @return void
	 */
	private function disable_settings(): void {
		add_filter(
			'vigetwp_admin_menu',
			function ( array $mods ): array {
				$mods[] = [
					'menu'    => 'options-general.php',
					'submenu' => 'options-discussion.php',
					'name'    => __( 'Avatars', 'viget-wp' ),
				];

				return $mods;
			}
		);
	}
}
