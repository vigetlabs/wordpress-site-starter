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
				?>
				<style>
                    #login h1 a,
                    .login h1 a {
                        background-image: url('<?php echo VIGETWP_PLUGIN_URL; ?>src/assets/images/viget-logo-transparent.svg');
                        background-repeat: no-repeat;
                        background-size: contain;
                        height: 65px;
                        width: 220px;
                    }
                    body.login {
                        position: relative;
                    }
                    body.login::after {
                        content: '';
                        background-image: url('<?php echo VIGETWP_PLUGIN_URL; ?>src/assets/images/viget-logo-transparent.svg');
                        background-repeat: no-repeat;
                        background-position: -10% 0;
                        background-size: 300%;
                        position: absolute;
                        inset: 0;
                        z-index: -1;
                        opacity: 0.075;
                    }
                    body.login.wp-core-ui .button-primary {
                        background-color: #1596BB;
                        transition: all 200ms ease-in-out;
                    }
                    body.login.wp-core-ui .button-primary:hover {
                        background-color: #0e6d88;
                    }

                    body.login #backtoblog a,
                    body.login #nav a,
                    body.login h1 a {
                        transition: all 200ms ease-in-out;
                    }

                    body.login #backtoblog a:hover,
                    body.login #nav a:hover,
                    body.login h1 a:hover {
                        color: #1596BB;
                    }

                    body.login input[type=checkbox]:focus, body.login input[type=color]:focus, body.login input[type=email]:focus, body.login input[type=password]:focus, input[type=text]:focus {
                        border-color: #1596BB !important;
                        box-shadow: 0 0 0 1px #1596BB !important;
                    }
				</style>
				<?php
			}
		);
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
