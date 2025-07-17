<?php
/**
 * LoginScreen Class
 *
 * @package VigetWP
 */

namespace VigetWP\Admin;

/**
 * LoginScreen Class
 */
class LoginScreen {

	/**
	 * LoginScreen constructor.
	 */
	public function __construct() {
		// Adjust the Login CSS
		$this->login_css();

		// Adjust the Login Logo URL
		$this->login_logo_url();

		// Adjust the Login Logo Text
		$this->login_logo_text();
	}

	/**
	 * Custom admin login css. Adjusts:
	 * - logo
	 * - background color
	 * - button color
	 * - link hover colors
	 */
	private function login_css(): void {
		add_action(
			'login_enqueue_scripts',
			function () {
				$logo_url = $this->get_logo_url();

				$brand_colors = apply_filters(
					'vigetwp_brand_colors',
					[
						'primary'   => '#1596BB',
						'secondary' => '#0e6d88',
						'accent'    => '#f26628',
					]
				);
				?>
				<style>
					:root {
						--brand-primary: <?php echo esc_html( $brand_colors['primary'] ); ?>;
						--brand-secondary: <?php echo esc_html( $brand_colors['secondary'] ); ?>;
						--brand-accent: <?php echo esc_html( $brand_colors['accent'] ); ?>;
					}

					<?php if ( $logo_url ) : ?>
						#login h1 a,
						.login h1 a {
							background-image: url('<?php echo esc_html( $logo_url ); ?>');
							background-repeat: no-repeat;
							background-size: contain;
							height: 65px;
							width: 220px;
						}
					<?php endif; ?>

					body.login {
						position: relative;
					}
					body.login::after {
						content: '';
						background-image: url('<?php echo esc_html( $logo_url ); ?>');
						background-repeat: no-repeat;
						background-position: -10% 0;
						background-size: 300%;
						position: absolute;
						inset: 0;
						z-index: -1;
						opacity: 0.075;
					}
					body.login.wp-core-ui .button-primary {
						background-color: var(--brand-primary);
						transition: all 200ms ease-in-out;
					}
					body.login.wp-core-ui .button-primary:hover {
						background-color: var(--brand-secondary);
					}

					body.login #backtoblog a,
					body.login #nav a,
					body.login h1 a {
						transition: all 200ms ease-in-out;
					}

					body.login #backtoblog a:hover,
					body.login #nav a:hover,
					body.login h1 a:hover {
						color: var(--brand-primary);
					}

					body.login input[type=checkbox]:focus, body.login input[type=color]:focus, body.login input[type=email]:focus, body.login input[type=password]:focus, input[type=text]:focus {
						border-color: var(--brand-primary) !important;
						box-shadow: 0 0 0 1px var(--brand-primary) !important;
					}
				</style>
				<?php
			}
		);
	}

	/**
	 * Get the logo URL.
	 *
	 * @return string
	 */
	private function get_logo_url(): string {
		$logo_url = VIGETWP_PLUGIN_URL . 'src/assets/images/logo.svg';
		if ( has_custom_logo() ) {
			$logo_url = wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' );
		}
		return $logo_url;
	}

	/**
	 * Change the login logo URL.
	 *
	 * @return void
	 */
	private function login_logo_url(): void {
		add_filter(
			'login_headerurl',
			fn() => home_url( '/' )
		);
	}

	/**
	 * Change the login logo text.
	 *
	 * @return void
	 */
	private function login_logo_text(): void {
		add_filter(
			'login_headertext',
			fn() => get_bloginfo( 'name' )
		);
	}
}
