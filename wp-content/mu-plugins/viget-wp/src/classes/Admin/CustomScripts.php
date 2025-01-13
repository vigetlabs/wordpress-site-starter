<?php
/**
 * Custom Scripts
 *
 * @package VigetWP
 */

namespace VigetWP\Admin;

/**
 * Custom Scripts
 */
class CustomScripts {

	/**
	 * Option name
	 *
	 * @var string
	 */
	const OPTION_NAME = 'vigetwp_custom_scripts';

	/**
	 * Page Slug
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'vigetwp-custom-scripts';

	/**
	 * Custom scripts
	 *
	 * @var array
	 */
	private array $scripts = [
		'head_open'  => '',
		'head_close' => '',
		'body_open'  => '',
		'body_close' => '',
	];

	/**
	 * CustomScripts constructor.
	 */
	public function __construct() {
		// Load custom scripts
		$this->load_scripts();

		// Save Custom Scripts settings
		$this->save_settings();

		// Add custom scripts to the Tools menu
		$this->scripts_menu();

		// Register Custom Scripts settings
		$this->register_settings();

		// Enqueue admin assets for the Custom Scripts page
		$this->enqueue_admin_assets();

		// Insert custom scripts
		$this->insert_scripts();
	}

	/**
	 * Load custom scripts
	 *
	 * @return void
	 */
	private function load_scripts(): void {
		$scripts = get_option( self::OPTION_NAME );
		if ( ! $scripts ) {
			return;
		}

		$this->scripts = $scripts;
	}

	/**
	 * Get custom scripts
	 *
	 * @param ?string $location Location of the script.
	 *
	 * @return string|array
	 */
	public function get_scripts( ?string $location = null ): string|array {
		if ( ! $location ) {
			return $this->scripts;
		}

		return $this->scripts[ $location ];
	}

	/**
	 * Register Custom Scripts settings
	 *
	 * @return void
	 */
	private function register_settings(): void {
		add_action(
			'admin_init',
			function() {
				register_setting( 'vigetwp_head_scripts', self::OPTION_NAME );

				add_settings_section(
					'vigetwp_head_scripts',
					__( 'Head Scripts', 'viget-wp' ),
					'__return_empty_string',
					self::OPTION_NAME
				);

				add_settings_field(
					'vigetwp_head_open_scripts',
					__( 'Head Open Scripts', 'viget-wp' ),
					function() {
						$script = $this->get_scripts( 'head_open' );
						?>
						<textarea name="<?php echo esc_attr( self::OPTION_NAME ); ?>[head_open]" id="vigetwp_head_open_scripts" class="large-text code wp-code-mirror" rows="10"><?php echo esc_textarea( $script ); ?></textarea>
						<?php
					},
					self::OPTION_NAME,
					'vigetwp_head_scripts'
				);

				add_settings_field(
					'vigetwp_head_close_scripts',
					__( 'Head Close Scripts', 'viget-wp' ),
					function() {
						$script = $this->get_scripts( 'head_close' );
						?>
						<textarea name="<?php echo esc_attr( self::OPTION_NAME ); ?>[head_close]" id="vigetwp_head_close_scripts" class="large-text code wp-code-mirror" rows="10"><?php echo esc_textarea( $script ); ?></textarea>
						<?php
					},
					self::OPTION_NAME,
					'vigetwp_head_scripts'
				);

				add_settings_section(
					'vigetwp_body_scripts',
					__( 'Body Scripts', 'viget-wp' ),
					'__return_empty_string',
					self::OPTION_NAME
				);

				add_settings_field(
					'vigetwp_body_open_scripts',
					__( 'Body Open Scripts', 'viget-wp' ),
					function() {
						$script = $this->get_scripts( 'body_open' );
						?>
						<textarea name="<?php echo esc_attr( self::OPTION_NAME ); ?>[body_open]" id="vigetwp_body_open_scripts" class="large-text code wp-code-mirror" rows="10"><?php echo esc_textarea( $script ); ?></textarea>
						<?php
					},
					self::OPTION_NAME,
					'vigetwp_body_scripts'
				);

				add_settings_field(
					'vigetwp_body_close_scripts',
					__( 'Body Close Scripts', 'viget-wp' ),
					function() {
						$script = $this->get_scripts( 'body_close' );
						?>
						<textarea name="<?php echo esc_attr( self::OPTION_NAME ); ?>[body_close]" id="vigetwp_body_close_scripts" class="large-text code wp-code-mirror" rows="10"><?php echo esc_textarea( $script ); ?></textarea>
						<?php
					},
					self::OPTION_NAME,
					'vigetwp_body_scripts'
				);
			}
		);
	}

	/**
	 * Save Custom Scripts settings
	 *
	 * @return void
	 */
	private function save_settings(): void {
		add_action(
			'admin_init',
			function() {
				if ( ! isset( $_POST['_' . self::OPTION_NAME . '_nonce'] ) ) {
					return;
				}

				if ( ! wp_verify_nonce( sanitize_key( $_POST['_' . self::OPTION_NAME . '_nonce'] ), self::PAGE_SLUG ) ) {
					add_settings_error( self::OPTION_NAME, self::OPTION_NAME, __( 'Invalid nonce.', 'viget-wp' ) );
					return;
				}

				$scripts = ! empty( $_POST[ self::OPTION_NAME ] ) ? $_POST[ self::OPTION_NAME ] : $this->scripts;
				$allowed = [
					'script'   => [
						'type'  => [],
						'src'   => [],
						'async' => [],
						'defer' => [],
					],
					'noscript' => [],
					'iframe'   => [
						'src'    => [],
						'width'  => [],
						'height' => [],
						'style'  => [],
					],
					'#text'    => [], // Allow inline script content
				];

				$style_filter = function( array $styles ): array {
					$styles[] = 'display';
					$styles[] = 'visibility';
					return $styles;
				};

				add_filter( 'safe_style_css', $style_filter );

				foreach ( $scripts as $key => $script ) {
					$script = wp_kses( wp_unslash( $script ), $allowed );
					$scripts[ $key ] = html_entity_decode( $script, ENT_QUOTES | ENT_HTML5 );
				}

				remove_filter( 'safe_style_css', $style_filter );

				update_option( self::OPTION_NAME, $scripts );

				$this->scripts = $scripts;

				add_settings_error( self::OPTION_NAME, self::OPTION_NAME, __( 'Settings saved.', 'viget-wp' ), 'updated' );
			},
			1
		);
	}

	/**
	 * Add custom scripts to the Tools menu
	 *
	 * @return void
	 */
	private function scripts_menu(): void {
		add_action(
			'admin_menu',
			function() {
				add_submenu_page(
					'tools.php',
					__( 'Custom Scripts', 'viget-wp' ),
					'Custom Scripts',
					'manage_options',
					self::PAGE_SLUG,
					[ $this, 'admin_page' ],
					100
				);
			}
		);
	}

	/**
	 * Custom Scripts Admin page
	 *
	 * @return void
	 */
	public function admin_page(): void {
		vigetwp()->load_view( 'admin/custom-scripts.php' );
	}

	/**
	 * Enqueue admin assets for the Custom Scripts page
	 *
	 * @return void
	 */
	private function enqueue_admin_assets(): void {
		add_action(
			'admin_enqueue_scripts',
			function ( string $hook ) {
				if ( 'tools_page_' . self::PAGE_SLUG !== $hook ) {
					return;
				}

				// Enqueue custom script
				wp_enqueue_script(
					self::PAGE_SLUG,
					VIGETWP_PLUGIN_URL . 'src/assets/js/custom-scripts.js',
					[ 'wp-codemirror' ],
					VIGETWP_VERSION
				);

				// Enqueue custom style
				wp_enqueue_style(
					'wp-codemirror-oceanic-next',
					'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.2/theme/oceanic-next.min.css',
					[ 'wp-codemirror' ],
					'5.59.2'
				);
			}
		);
	}

	/**
	 * Insert custom scripts
	 *
	 * @return void
	 */
	private function insert_scripts(): void {
		add_action(
			'wp_head',
			function() {
				echo $this->get_scripts( 'head_open' );
			},
			1
		);

		add_action(
			'wp_head',
			function() {
				echo $this->get_scripts( 'head_close' );
			},
			99999
		);

		add_action(
			'wp_body_open',
			function() {
				echo $this->get_scripts( 'body_open' );
			},
			1
		);

		add_action(
			'wp_footer',
			function() {
				echo $this->get_scripts( 'body_close' );
			},
			99999
		);
	}
}
