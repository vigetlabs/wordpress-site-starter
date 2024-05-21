<?php
/**
 * VigetWP Core Class
 *
 * @package VigetWP
 * @since 1.0.0
 */

namespace VigetWP;

use VigetWP\Admin\Assets;
use VigetWP\Admin\DashboardWidgets;
use VigetWP\Admin\FileEditors;
use VigetWP\Admin\LoginScreen;
use VigetWP\Admin\TinyMCE;
use VigetWP\Features\DisableComments;
use VigetWP\Features\Gravatar;
use VigetWP\Plugins\ACF\GravityForms;
use VigetWP\Plugins\ACF\Toolbars;
use VigetWP\Admin\AdminBar;
use VigetWP\Admin\ColorScheme;
use VigetWP\Admin\Footer;
use VigetWP\Admin\Menu;

/**
 * Core Class
 */
class Core {

	/**
	 * @since 1.0.0
	 * @var ?Core
	 */
	private static ?Core $instance = null;

	/**
	 * @since 1.0.0
	 * @var bool
	 */
	private bool $initialized = false;

	/**
	 * @since 1.0.0
	 * @var ?Menu
	 */
	public ?Menu $admin_menu = null;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( ! $this->initialized ) {
			$this->init();
		}
	}

	/**
	 * Get the singleton instance of this class
	 *
	 * @since 1.0.0
	 *
	 * @return Core
	 */
	public static function get_instance(): Core {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get the plugin version
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_version(): string {
		$data = get_plugin_data( VIGETWP_PLUGIN_FILE );
		return $data['Version'];
	}

	/**
	 * Initialize the plugin
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init(): void {
		// Load plugin dependencies and modules.
		$this->load_dependencies();
		$this->load_modules();
		$this->init_modules();

		// Load the text domain for translations.
		$this->load_text_domain();

		// Restrict REST API access.
		$this->restrict_rest_api_access();

		// Disable CSS concatenation.
		$this->disable_css_concatenation();

		$this->initialized = true;
	}

	/**
	 * Load plugin dependencies.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function load_dependencies(): void {
		require_once VIGETWP_PLUGIN_PATH . 'src/helpers.php';
	}

	/**
	 * Initialize Modules after Viget WP has initialized.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function load_modules(): void {
		add_action(
			'mu_plugin_loaded',
			function () {
				/**
				 * Loading order here is important.
				 * Some modules depend on others being loaded first.
				 */

				$this->admin_menu = new Menu();
			}
		);
	}

	/**
	 * Perform any manual module initialization.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function init_modules(): void {
		// Features
		new DisableComments();
		new Gravatar();

		// Admin
		new Assets();
		new ColorScheme();
		new AdminBar();
		new LoginScreen();
		new Footer();
		new TinyMCE();
		new DashboardWidgets();
		new FileEditors();

		## Plugins

		// ACF
		new Toolbars();
		new GravityForms();
	}

	/**
	 * Get path to a view file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public function get_view_path( string $name ): string {
		$path = get_stylesheet_directory() . '/viget-wp/' . $name;

		if ( ! file_exists( $path ) ) {
			$path = VIGETWP_PLUGIN_PATH . 'views/' . $name;
		}

		return apply_filters( 'vigetwp_view_path', $path, $name );
	}

	/**
	 * Load a view file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $_name
	 * @param array  $_data
	 *
	 * @return void
	 */
	public function load_view( string $_name, array $_data = [] ): void {
		$_path = $this->get_view_path( $_name );

		if ( ! file_exists( $_path ) ) {
			return;
		}

		extract( $_data ); // phpcs:ignore

		require $_path;
	}

	/**
	 * Get the contents of a view file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $_name
	 * @param array $_data
	 *
	 * @return string
	 */
	public function get_view( string $_name, array $_data = [] ): string {
		ob_start();
		$this->load_view( $_name, $_data );
		return ob_get_clean();
	}

	/**
	 * Load Text Domain
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_text_domain(): void {
		add_action(
			'init',
			function () {
				load_plugin_textdomain(
					'viget-wp',
					false,
					VIGETWP_PLUGIN_PATH . 'languages'
				);
			}
		);
	}

	/**
	 * Only allow authenticated users with specific roles to access parts of the REST API.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function restrict_rest_api_access(): void {
		add_filter(
			'rest_endpoints',
			function ( $endpoints ) {
				// Allow permission-based access to the secure REST API endpoints.
				if ( current_user_can( 'edit_posts' ) ) {
					return $endpoints;
				}

				$restricted = [
					'/wp/v2/users',
					'/wp/v2/users/(?P<id>[\d]+)',
				];

				foreach ( $restricted as $endpoint ) {
					if ( isset( $endpoints[ $endpoint ] ) ) {
						unset( $endpoints[ $endpoint ] );
					}
				}

				return $endpoints;
			}
		);
	}

	/**
	 * Disable CSS concatenation
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function disable_css_concatenation(): void {
		add_filter( 'css_do_concat', '__return_false' );
	}
}
