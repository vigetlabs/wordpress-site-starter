<?php
/**
 * Inspired from:
 *
 * @source https://github.com/andrefelipe/vite-php-setup
 * @package WPStarter
 */

namespace WPStarter;

/**
 * This class handles loading Vite assets.
 */
class Vite {

	/**
	 * Instance of this class.
	 *
	 * @var ?Vite
	 */
	private static ?Vite $instance = null;

	/**
	 * Storage of environment variables.
	 *
	 * @var string
	 */
	private string $env;

	/**
	 * The site URL.
	 *
	 * @var string
	 */
	private string $site_url;

	/**
	 * The port to be used.
	 *
	 * @var int
	 */
	private string $port;

	/**
	 * The dev server.
	 *
	 * @var string
	 */
	private string $dev_server;

	/**
	 * The URL to the dist folder.
	 *
	 * @var string
	 */
	private string $dist_url;

	/**
	 * The path to the dist folder.
	 *
	 * @var string
	 */
	private string $dist_path;

	/**
	 * The entry files.
	 *
	 * @var array
	 */
	private array $entries = [];

	/**
	 * The manifest.
	 *
	 * @var array
	 */
	private array $manifest = [];

	/**
	 * The variables to localize.
	 *
	 * @var array
	 */
	private array $vars = [];

	/**
	 * Whether this class has been initialized.
	 *
	 * @var bool
	 */
	private bool $initialized = false;

	/**
	 * If admin notice has been printed.
	 *
	 * @var bool
	 */
	private bool $did_admin_notice = false;

	/**
	 * Set up vars and hooks.
	 */
	public function __construct() {
		if ( $this->initialized ) {
			return;
		}

		$this->initialized = true;
		$this->site_url    = get_site_url();
		$this->port        = is_ssl() ? getenv( 'VITE_PRIMARY_PORT' ) : getenv( 'VITE_SECONDARY_PORT' );
		$this->dev_server  = "{$this->site_url}:{$this->port}";

		$this->dist_url  = get_stylesheet_directory_uri() . '/dist/';
		$this->dist_path = get_stylesheet_directory() . '/dist/.vite/';

		$this->env = getenv( 'ENVIRONMENT' );

		// Set front-end CSS/JS.
		$this->add_entry( 'main.js', 'default' );
		$this->add_entry( 'main.js' );

		// Set editor CSS/JS.
		$this->add_entry( 'main.js', 'editor' );

		// Set admin CSS/JS.
		$this->add_entry( 'admin.js' );

		add_filter( 'script_loader_tag', [ $this, 'script_loader' ], 10, 3 );
		add_filter( 'style_loader_tag', [ $this, 'style_loader' ], 10, 4 );

		// Initialize Vite.
		add_action( 'init', [ $this, 'init' ], 100 );

		// Load block editor assets.
		$this->block_assets( 'editor' );
	}

	/**
	 * Get the instance of this class.
	 *
	 * @return Vite
	 */
	public static function get_instance(): Vite {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize
	 *
	 * @param string $entry Entry key.
	 *
	 * @return void
	 */
	public function init( string $entry = '' ): void {
		if ( ! $entry ) {
			$entry = is_admin() ? 'admin' : 'default';
		}

		if ( ! $this->get_entry( $entry ) ) {
			return;
		}

		$this->vite( $entry );
	}

	/**
	 * Add a new entry point file.
	 *
	 * @param string $entry The entry point file.
	 * @param string $key   The callable array key index.
	 *
	 * @return void
	 */
	public function add_entry( string $entry, string $key = '' ): void {
		if ( ! $key ) {
			$key = rtrim( $entry, '.js' );
		}

		if ( \array_key_exists( $key, $this->entries ) ) {
			// Log: Entry key already exists.
			return;
		}

		$this->entries[ $key ] = $entry;
	}

	/**
	 * Get entry point file by key.
	 *
	 * @param string $entry The entry point key.
	 *
	 * @return string|false
	 */
	public function get_entry( string $entry = '' ): string|false {
		if ( ! $entry ) {
			$entry = 'default';
		}

		if ( \array_key_exists( $entry, $this->entries ) ) {
			return $this->entries[ $entry ];
		}

		return false;
	}

	/**
	 * Localize variables for theme scripts.
	 *
	 * @param string $name The name of the script.
	 * @param array $values The values to localize.
	 *
	 * @return void
	 */
	public function localize_vars( $name, $values ): void {
		$this->vars[ $name ] = $values;
	}

	/**
	 * Prints all the html entries needed for Vite
	 *
	 * @param string $entry The entry point file.
	 *
	 * @return void
	 */
	public function vite( string $entry ) {
		if ( 'dev' === $this->env ) {
			$scripts = [];
			$file    = $this->get_entry( $entry );

			if ( ! $this->initialized ) {
				$scripts[] = \sprintf(
					'<script type="module" src="%s/@vite/client" id="vite-client"></script>',
					esc_url( $this->dev_server )
				);
			}

			$scripts[] = \sprintf(
				'<script type="module" src="%s/%s" id="vite-entry-%s"></script>',
				esc_url( $this->dev_server ),
				esc_attr( $file ),
				esc_attr( $entry )
			);

			foreach ( $this->vars as $name => $values ) {
				$scripts[] = \sprintf(
					'<script id="vite-var-%s">window.%s = %s;</script>',
					esc_attr( $name ),
					esc_js( $name ),
					json_encode( $values )
				);
			}

			$hook = is_admin() ? 'admin_head' : 'wp_head';
			add_action(
				$hook,
				function () use ( $scripts ) {
					echo implode( PHP_EOL, $scripts );
				},
				100
			);

			return;
		}

		$this->js( $entry );
		$this->imports( $entry );
		$this->css( $entry );
	}

	/**
	 * Load JS files.
	 *
	 * @param string $entry The entry point file.
	 *
	 * @return void
	 */
	private function js( string $entry ): void {
		$file = $this->get_entry( $entry );
		$url  = $this->get_asset_url( $file );

		if ( ! $url ) {
			return;
		}

		$manifest = $this->get_manifest();

		if ( ! empty( $manifest[ $file ] ) ) {
			$hook = is_admin() ? 'admin_enqueue_scripts' : 'wp_enqueue_scripts';
			add_action(
				$hook,
				function () use ( $url, $entry, $file, $manifest ) {
					$version       = $manifest[ $file ]['version'] ?? wp_get_theme()->get( 'Version' );
					$dependencies  = $manifest[ $file ]['dependencies'] ?? [];
					$script_handle = 'theme-script-' . $manifest[ $file ]['name'];

					wp_register_script(
						$script_handle,
						$url,
						$dependencies,
						$version,
						[ 'in_footer' => true ]
					);

					foreach ( $this->vars as $name => $values ) {
						wp_localize_script(
							$script_handle,
							$name,
							$values
						);
					}

					wp_enqueue_script( $script_handle );
				}
			);

			return;
		}

		$hook = is_admin() ? 'admin_head' : 'wp_head';
		add_action(
			$hook,
			function () use ( $url, $entry ) {
				\printf(
					'<script type="module" crossorigin src="%s" id="vite-entry-%s"></script>',
					esc_url( $url ),
					esc_attr( $entry )
				);
			}
		);
	}

	/**
	 * Load import URLs.
	 *
	 * @param string $entry The entry point file.
	 *
	 * @return void
	 */
	private function imports( string $entry ): void {
		$file     = $this->get_entry( $entry );
		$manifest = $this->get_manifest();

		foreach ( $this->get_imports( $file ) as $index => $url ) {
			if ( ! empty( $manifest[ $file ] ) ) {
				$hook = is_admin() ? 'admin_enqueue_scripts' : 'wp_enqueue_scripts';
				add_action(
					$hook,
					function () use ( $url, $manifest, $entry, $file, $index ) {
						$version      = $manifest[ $file ]['version'] ?? wp_get_theme()->get( 'Version' );
						$dependencies = $manifest[ $file ]['dependencies'] ?? [];
						$suffix       = \count( $manifest[ $file ]['imports'] ) > 1 ? '' : $index + 1;
						wp_enqueue_style(
							'theme-style-preload-' . $manifest[ $file ]['name'] . $suffix,
							$url,
							$dependencies,
							$version,
							'all'
						);
					}
				);
			} else {
				$hook = is_admin() ? 'admin_head' : 'wp_head';
				add_action(
					$hook,
					function () use ( $url ) {
						\printf(
							'<link rel="modulepreload" href="%s">',
							esc_url( $url )
						);
					}
				);
			}
		}
	}

	/**
	 * Load CSS files.
	 *
	 * @param string $entry The entry point file.
	 *
	 * @return void
	 */
	private function css( string $entry ): void {
		$file     = $this->get_entry( $entry );
		$manifest = $this->get_manifest();

		foreach ( $this->get_css( $file ) as $index => $url ) {
			if ( ! empty( $manifest[ $file ] ) ) {
				$hook = is_admin() ? 'admin_enqueue_scripts' : 'wp_enqueue_scripts';
				add_action(
					$hook,
					function () use ( $url, $entry, $file, $manifest, $index ) {
						$version      = $manifest[ $file ]['version'] ?? wp_get_theme()->get( 'Version' );
						$dependencies = $manifest[ $file ]['dependencies'] ?? [];
						$suffix       = \count( $manifest[ $file ]['css'] ) > 1 ? '' : $index + 1;

						wp_enqueue_style(
							'theme-style-' . $manifest[ $file ]['name'] . $suffix,
							$url,
							$dependencies,
							$version,
							'all'
						);
					}
				);
			} else {
				$hook = is_admin() ? 'admin_head' : 'wp_head';
				add_action(
					$hook,
					function () use ( $url ) {
						\printf(
							'<link rel="stylesheet" href="%s">',
							esc_url( $url )
						);
					}
				);
			}
		}
	}

	/**
	 * Helper to locate build files
	 *
	 * @return array
	 */
	public function get_manifest(): array {
		if ( ! empty( $this->manifest ) ) {
			return $this->manifest;
		}

		$manifest = $this->dist_path . '/manifest.json';

		if ( ! file_exists( $manifest ) ) {
			if ( ! $this->did_admin_notice ) {
				return [];
			}

			add_action(
				'admin_notices',
				function () {
					printf(
						'<div class="notice notice-warning is-dismissible">
						<p>%s</p>
					</div>',
						esc_html__( 'Manifest.json file is missing. Run "ddev npm run build" to fix.', 'wp-starter' )
					);
				}
			);

			$this->did_admin_notice = true;

			return [];
		}

		$contents = file_get_contents( $manifest );

		if ( ! $contents ) {
			die( esc_html__( 'Error: The manifest.json file is empty or doesn\'t exist.', 'wp-starter' ) );
		}

		$this->manifest = json_decode( $contents, true );

		return $this->manifest;
	}

	/**
	 * Get Asset URL
	 *
	 * @param string $entry The entry point file.
	 *
	 * @return string
	 */
	public function get_asset_url( string $entry ): string {
		$manifest = $this->get_manifest();

		if ( ! isset( $manifest[ $entry ] ) ) {
			die( esc_html__( 'Could not find entry in manifest for', 'wp-starter' ) . ' ' . esc_html( $entry ) );
		}

		return $this->dist_url . $manifest[ $entry ]['file'];
	}

	/**
	 * Get import URLs.
	 *
	 * @param string $entry The entry point file.
	 *
	 * @return array
	 */
	private function get_imports( string $entry ): array {
		$urls     = [];
		$manifest = $this->get_manifest();

		if ( ! empty( $manifest[ $entry ]['imports'] ) ) {
			foreach ( $manifest[ $entry ]['imports'] as $imports ) {
				$urls[] = $this->dist_url . $manifest[ $imports ]['file'];
			}
		}

		return $urls;
	}

	/**
	 * Get CSS URLs
	 *
	 * @param string $entry The entry point file.
	 *
	 * @return array
	 */
	private function get_css( string $entry_file ): array {
		$urls     = [];
		$manifest = $this->get_manifest();

		if ( ! empty( $manifest[ $entry_file ]['css'] ) ) {
			foreach ( $manifest[ $entry_file ]['css'] as $file ) {
				$urls[] = $this->dist_url . $file;
			}
		}

		return $urls;
	}

	/**
	 * Adjust theme script tags.
	 *
	 * @param string $tag    The generated tag (markup).
	 * @param string $handle The script handle.
	 * @param string $src    The script source.
	 *
	 * @return string
	 */
	public function script_loader( string $tag, string $handle, string $src ): string {
		$theme_handles = [ 'theme-script-', 'theme-vite-canvas-' ];
		$is_theme      = array_reduce( $theme_handles, fn( $carry, $prefix ) => $carry || str_contains( $handle, $prefix ), false );

		if ( ! $is_theme ) {
			return $tag;
		}

		return str_replace( '<script ', '<script type="module" crossorigin ', $tag );
	}

	/**
	 * Adjust theme style tags.
	 *
	 * @param string $tag    The generated tag (markup).
	 * @param string $handle The style handle.
	 * @param string $href   The style URL.
	 * @param string $media  The style media.
	 *
	 * @return string
	 */
	public function style_loader( string $tag, string $handle, string $href, string $media ): string {
		if ( ! str_contains( $handle, 'theme-style-preload-' ) ) {
			return $tag;
		}

		return str_replace( '<link ', '<link rel="modulepreload" ', $tag );
	}

	/**
	 * Enqueue Vite assets into the editor canvas iframe head.
	 *
	 * The editor canvas is an iframe that renders the block content. Assets are
	 * collected via _wp_get_iframed_editor_assets() which runs enqueue_block_assets
	 * with should_load_block_editor_scripts_and_styles returning false.
	 *
	 * @param string $entry The entry point key.
	 */
	public function block_assets( string $entry = 'editor' ): void {
		if ( ! $this->get_entry( $entry ) ) {
			return;
		}

		$file = $this->get_entry( $entry );

		add_action(
			'enqueue_block_assets',
			function () use ( $file, $entry ) {
				// Only enqueue when collecting assets for the editor canvas iframe.
				if ( apply_filters( 'should_load_block_editor_scripts_and_styles', true ) ) {
					return;
				}

				if ( 'dev' === $this->env ) {
					$vite_client_url = $this->dev_server . '/@vite/client';
					$vite_entry_url  = $this->dev_server . '/' . $file;

					wp_enqueue_script(
						'theme-vite-canvas-client',
						$vite_client_url,
						[],
						wp_get_theme()->get( 'Version' ),
						false
					);
					wp_enqueue_script(
						'theme-vite-canvas-entry',
						$vite_entry_url,
						[ 'theme-vite-canvas-client' ],
						wp_get_theme()->get( 'Version' ),
						false
					);
				} else {
					$script_url   = $this->get_asset_url( $file );
					$manifest     = $this->get_manifest();
					$dependencies = $manifest[ $file ]['dependencies'] ?? [];

					wp_register_script(
						'theme-vite-canvas-entry',
						$script_url,
						$dependencies,
						wp_get_theme()->get( 'Version' ),
						false
					);

					foreach ( $this->vars as $name => $values ) {
						wp_localize_script(
							'theme-vite-canvas-entry',
							$name,
							$values
						);
					}

					wp_enqueue_script( 'theme-vite-canvas-entry' );

					// Use same handle format as css() so getCompatibilityStyles() finds them
					// in the iframe and doesn't warn about "added incorrectly".
					foreach ( $this->get_css( $file ) as $index => $url ) {
						if ( ! empty( $manifest[ $file ] ) ) {
							$version      = $manifest[ $file ]['version'] ?? wp_get_theme()->get( 'Version' );
							$dep          = $manifest[ $file ]['dependencies'] ?? [];
							$suffix       = \count( $manifest[ $file ]['css'] ) > 1 ? '' : $index + 1;
							$style_handle = 'theme-style-' . $manifest[ $file ]['name'] . $suffix;

							wp_enqueue_style(
								$style_handle,
								$url,
								$dep,
								$version,
								'all'
							);
						} else {
							wp_enqueue_style(
								'theme-vite-canvas-style-' . $index,
								$url,
								[],
								wp_get_theme()->get( 'Version' )
							);
						}
					}
				}
			},
			5
		);
	}
}
