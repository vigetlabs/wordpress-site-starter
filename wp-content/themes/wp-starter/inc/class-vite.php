<?php
/**
 * Vite asset loader for WordPress.
 *
 * @package WPStarter
 */

namespace WPStarter;

/**
 * Handles loading Vite assets in development and production modes.
 *
 * ## Built-in entry points
 *
 * | Key     | File        | Hook                         |
 * |---------|-------------|------------------------------|
 * | default | main.js     | wp_enqueue_scripts           |
 * | admin   | admin.js    | admin_enqueue_scripts        |
 * | editor  | editor.js   | enqueue_block_editor_assets  |
 *
 * The editor entry imports virtual:editor-scoped-styles, which the
 * vite-scoped-editor-styles Vite plugin uses to inject CSS scoped to
 * .editor-styles-wrapper — replicating add_editor_style() in both
 * dev (with HMR) and prod.
 *
 * ## Entry mirroring for add_css_dependency(), add_js_dependency(), localize_vars()
 *
 * | $entry value   | Effect                                        |
 * |----------------|-----------------------------------------------|
 * | null (default) | Adds to 'default' AND mirrors to 'editor'    |
 * | 'default'      | Same as null — mirrors to 'editor'            |
 * | 'main'         | Adds only to 'default'; editor does NOT get it |
 * | 'editor'       | Adds only to 'editor'                         |
 * | 'admin'        | Adds only to 'admin'                          |
 *
 * ## Singleton
 *
 * Always obtain the instance via Vite::get_instance(). Hooks and filters are
 * registered once in the constructor; subsequent calls to get_instance() return
 * the cached instance without re-running any setup.
 */
class Vite {

	/**
	 * Singleton instance.
	 *
	 * @var ?Vite
	 */
	private static ?Vite $instance = null;

	/**
	 * ENVIRONMENT env var value ('dev' enables dev mode).
	 *
	 * @var string
	 */
	private string $env;

	/**
	 * WordPress site URL used to build the dev server base URL.
	 *
	 * @var string
	 */
	private string $site_url;

	/**
	 * Vite dev server port number (as a string from getenv).
	 *
	 * @var string
	 */
	private string $port;

	/**
	 * Full Vite dev server base URL
	 *
	 * @var string
	 */
	private string $dev_server;

	/**
	 * URL to the theme dist/ folder.
	 *
	 * @var string
	 */
	private string $dist_url;

	/**
	 * Filesystem path to dist/.vite/ (for reading manifest.json).
	 *
	 * @var string
	 */
	private string $dist_path;

	/**
	 * Registered entries: key → JS filename relative to Vite root.
	 *
	 * @var array<string, string>
	 */
	private array $entries = [];

	/**
	 * Cached manifest.json contents.
	 *
	 * @var array
	 */
	private array $manifest = [];

	/**
	 * Per-entry CSS dependency WP style handles.
	 *
	 * @var array<string, string[]>
	 */
	private array $css_deps = [];

	/**
	 * Per-entry JS dependency WP script handles.
	 *
	 * @var array<string, string[]>
	 */
	private array $js_deps = [];

	/**
	 * Per-entry wp_localize_script() payloads: entry → [var_name => values].
	 *
	 * @var array<string, array<string, mixed>>
	 */
	private array $vars = [];

	/**
	 * Whether the "manifest missing" admin notice has been queued.
	 *
	 * @var bool
	 */
	private bool $did_manifest_notice = false;

	// -------------------------------------------------------------------------
	// Singleton
	// -------------------------------------------------------------------------

	/**
	 * Return the singleton instance, constructing it on first call.
	 *
	 * @return Vite
	 */
	public static function get_instance(): Vite {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	// -------------------------------------------------------------------------
	// Construction — called exactly once by get_instance()
	// -------------------------------------------------------------------------

	/**
	 * Configure properties and register all hooks and filters.
	 *
	 * Do not call directly — use Vite::get_instance() instead.
	 */
	public function __construct() {
		$this->site_url   = get_site_url();
		$this->port       = (string) ( is_ssl()
			? getenv( 'VITE_PRIMARY_PORT' )
			: getenv( 'VITE_SECONDARY_PORT' ) );
		$this->dev_server = "{$this->site_url}:{$this->port}";
		$this->dist_url   = get_stylesheet_directory_uri() . '/dist/';
		$this->dist_path  = get_stylesheet_directory() . '/dist/.vite/';
		$this->env        = (string) getenv( 'ENVIRONMENT' );

		// Register built-in entry points.
		$this->add_entry( 'main.js',   'default' );
		$this->add_entry( 'admin.js',  'admin' );
		$this->add_entry( 'editor.js', 'editor' );

		// Add type="module" crossorigin to all Vite-owned script tags.
		add_filter( 'script_loader_tag', [ $this, 'script_loader_tag' ], 10, 3 );

		// Enqueue each entry on the appropriate WordPress hook.
		add_action( 'wp_enqueue_scripts',         fn() => $this->enqueue_entry( 'default' ) );
		add_action( 'admin_enqueue_scripts',       fn() => $this->enqueue_entry( 'admin' ) );
		add_action( 'enqueue_block_editor_assets', fn() => $this->enqueue_entry( 'editor' ) );
	}

	// -------------------------------------------------------------------------
	// Public API
	// -------------------------------------------------------------------------

	/**
	 * Whether the current environment is dev mode.
	 *
	 * @return bool
	 */
	public function is_dev(): bool {
		return 'dev' === $this->env;
	}

	/**
	 * Register a Vite entry point.
	 *
	 * @param string $file JS filename relative to the Vite root (e.g. 'main.js').
	 * @param string $key  PHP key used to reference this entry. Defaults to the
	 *                     base filename without extension.
	 */
	public function add_entry( string $file, string $key = '' ): void {
		if ( ! $key ) {
			$key = pathinfo( $file, PATHINFO_FILENAME );
		}

		if ( array_key_exists( $key, $this->entries ) ) {
			return;
		}

		$this->entries[ $key ] = $file;

		// Initialise per-entry storage so callers never hit undefined key warnings.
		$this->css_deps[ $key ] ??= [];
		$this->js_deps[ $key ]  ??= [];
		$this->vars[ $key ]     ??= [];
	}

	/**
	 * Return the entry filename for a given key, or false if not found.
	 *
	 * @param string $key Entry key. Defaults to 'default'.
	 * @return string|false
	 */
	public function get_entry( string $key = 'default' ): string|false {
		return $this->entries[ $key ] ?? false;
	}

	/**
	 * Register a WP style handle as a CSS dependency for an entry.
	 *
	 * The style must be registered with wp_register_style() before the
	 * enqueue hook fires (e.g. during 'init').
	 *
	 * @param string      $handle WP style handle.
	 * @param string|null $entry  See class-level mirroring table. Default null.
	 */
	public function add_css_dependency( string $handle, ?string $entry = null ): void {
		foreach ( $this->resolve_entries( $entry ) as $key ) {
			$this->css_deps[ $key ] ??= [];
			$this->css_deps[ $key ][] = $handle;
		}
	}

	/**
	 * Register a WP script handle as a JS dependency for an entry.
	 *
	 * The script must be registered with wp_register_script() before the
	 * enqueue hook fires.
	 *
	 * @param string      $handle WP script handle.
	 * @param string|null $entry  See class-level mirroring table. Default null.
	 */
	public function add_js_dependency( string $handle, ?string $entry = null ): void {
		foreach ( $this->resolve_entries( $entry ) as $key ) {
			$this->js_deps[ $key ] ??= [];
			$this->js_deps[ $key ][] = $handle;
		}
	}

	/**
	 * Store a wp_localize_script() payload to inject when the entry is enqueued.
	 *
	 * @param string      $name   JavaScript global variable name.
	 * @param array       $values Values to expose on window[name].
	 * @param string|null $entry  See class-level mirroring table. Default null.
	 */
	public function localize_vars( string $name, array $values, ?string $entry = null ): void {
		foreach ( $this->resolve_entries( $entry ) as $key ) {
			$this->vars[ $key ] ??= [];
			$this->vars[ $key ][ $name ] = $values;
		}
	}

	/**
	 * Return the parsed Vite manifest, loading from disk on first call.
	 *
	 * Returns an empty array if the manifest is missing (prod build not yet run)
	 * and queues a one-time admin notice.
	 *
	 * @return array
	 */
	public function get_manifest(): array {
		if ( ! empty( $this->manifest ) ) {
			return $this->manifest;
		}

		$path = $this->dist_path . 'manifest.json';

		if ( ! file_exists( $path ) ) {
			$this->maybe_show_manifest_notice();
			return [];
		}

		$contents = file_get_contents( $path );

		if ( ! $contents ) {
			$this->maybe_show_manifest_notice();
			return [];
		}

		$decoded = json_decode( $contents, true );

		if ( ! is_array( $decoded ) ) {
			$this->maybe_show_manifest_notice();
			return [];
		}

		$this->manifest = $decoded;
		return $this->manifest;
	}

	/**
	 * Return the absolute URL to a built JS asset for a given entry key.
	 *
	 * @param string $key Entry key.
	 * @return string URL or empty string if the manifest or entry is unavailable.
	 */
	public function get_asset_url( string $key ): string {
		$file     = $this->get_entry( $key );
		$manifest = $this->get_manifest();

		if ( ! $file || empty( $manifest[ $file ]['file'] ) ) {
			return '';
		}

		return $this->dist_url . $manifest[ $file ]['file'];
	}

	// -------------------------------------------------------------------------
	// Filters
	// -------------------------------------------------------------------------

	/**
	 * Add type="module" crossorigin to Vite-owned script tags.
	 *
	 * Matches handles with these prefixes:
	 *   - theme-vite-client   (/@vite/client in dev)
	 *   - theme-vite-entry-*  (entry scripts in dev)
	 *   - theme-script-*      (built entry scripts in prod)
	 *
	 * @param string $tag    Full <script> tag markup.
	 * @param string $handle Script handle.
	 * @param string $src    Script source URL.
	 * @return string
	 */
	public function script_loader_tag( string $tag, string $handle, string $src ): string {
		$prefixes = [ 'theme-vite-client', 'theme-vite-entry-', 'theme-script-' ];

		$is_module = array_reduce(
			$prefixes,
			fn( $carry, $prefix ) => $carry || str_starts_with( $handle, $prefix ),
			false
		);

		if ( ! $is_module ) {
			return $tag;
		}

		return str_replace( '<script ', '<script type="module" crossorigin ', $tag );
	}

	// -------------------------------------------------------------------------
	// Private — entry loading
	// -------------------------------------------------------------------------

	/**
	 * Resolve the $entry parameter to concrete entry keys, applying mirroring.
	 *
	 * @param string|null $entry
	 * @return string[]
	 */
	private function resolve_entries( ?string $entry ): array {
		return match ( $entry ) {
			null, 'default' => [ 'default', 'editor' ],
			'main'          => [ 'default' ],
			default         => [ $entry ],
		};
	}

	/**
	 * Top-level dispatch: load one entry in dev or prod mode.
	 *
	 * @param string $key Entry key.
	 */
	private function enqueue_entry( string $key ): void {
		if ( ! $this->get_entry( $key ) ) {
			return;
		}

		if ( $this->is_dev() ) {
			$this->enqueue_dev( $key );
		} else {
			$this->enqueue_prod_js( $key );
			$this->enqueue_prod_css( $key );
			$this->enqueue_preloads( $key );
		}

		// CSS deps (e.g. Typekit) enqueued in both dev and prod.
		$this->enqueue_css_deps( $key );
	}

	/**
	 * Dev mode: register and enqueue the Vite HMR client and entry script.
	 *
	 * 'theme-vite-client' is registered once; WordPress deduplicates if
	 * multiple hooks call enqueue_dev() on the same admin page.
	 *
	 * @param string $key Entry key.
	 */
	private function enqueue_dev( string $key ): void {
		$file   = $this->get_entry( $key );
		$handle = 'theme-vite-entry-' . $key;

		// Vite HMR client — fixed handle, WP deduplicates on repeat enqueues.
		wp_register_script(
			'theme-vite-client',
			$this->dev_server . '/@vite/client',
			[],
			null,
			[ 'in_footer' => false ]
		);

		// Entry module — must load in <head> so Vite HMR sockets initialise early.
		wp_register_script(
			$handle,
			$this->dev_server . '/' . $file,
			array_merge( [ 'theme-vite-client' ], $this->js_deps[ $key ] ?? [] ),
			null,
			[ 'in_footer' => false ]
		);

		foreach ( $this->vars[ $key ] ?? [] as $name => $values ) {
			wp_localize_script( $handle, $name, $values );
		}

		wp_enqueue_script( $handle );
	}

	/**
	 * Prod mode: register and enqueue the built JS file from the manifest.
	 *
	 * @param string $key Entry key.
	 */
	private function enqueue_prod_js( string $key ): void {
		$file     = $this->get_entry( $key );
		$manifest = $this->get_manifest();

		if ( empty( $manifest[ $file ] ) ) {
			return;
		}

		$url    = $this->dist_url . $manifest[ $file ]['file'];
		$ver    = $manifest[ $file ]['version'] ?? wp_get_theme()->get( 'Version' );
		$deps   = array_merge(
			$manifest[ $file ]['dependencies'] ?? [],
			$this->js_deps[ $key ] ?? []
		);
		$handle = 'theme-script-' . $key;

		wp_register_script( $handle, $url, $deps, $ver, [ 'in_footer' => true ] );

		foreach ( $this->vars[ $key ] ?? [] as $name => $values ) {
			wp_localize_script( $handle, $name, $values );
		}

		wp_enqueue_script( $handle );
	}

	/**
	 * Prod mode: enqueue CSS files listed in the manifest for this entry.
	 *
	 * For the 'editor' entry this is typically a no-op because editor.js uses
	 * virtual:editor-scoped-styles, which bundles the scoped CSS inside the JS.
	 *
	 * @param string $key Entry key.
	 */
	private function enqueue_prod_css( string $key ): void {
		$file     = $this->get_entry( $key );
		$manifest = $this->get_manifest();

		if ( empty( $manifest[ $file ]['css'] ) ) {
			return;
		}

		$ver          = $manifest[ $file ]['version'] ?? wp_get_theme()->get( 'Version' );
		$css_dep_handles = $this->css_deps[ $key ] ?? [];

		foreach ( $manifest[ $file ]['css'] as $index => $css_file ) {
			$suffix = $index > 0 ? '-' . $index : '';
			wp_enqueue_style(
				'theme-style-' . $key . $suffix,
				$this->dist_url . $css_file,
				$css_dep_handles,
				$ver
			);
		}
	}

	/**
	 * Enqueue external CSS dependencies registered for an entry (both modes).
	 *
	 * @param string $key Entry key.
	 */
	private function enqueue_css_deps( string $key ): void {
		foreach ( $this->css_deps[ $key ] ?? [] as $dep_handle ) {
			if ( wp_style_is( $dep_handle, 'registered' ) ) {
				wp_enqueue_style( $dep_handle );
			}
		}
	}

	/**
	 * Prod mode: emit <link rel="modulepreload"> tags for Rollup dynamic imports.
	 *
	 * Called from within an enqueue hook callback, so we schedule output via
	 * the corresponding head action which fires later in the same request.
	 *
	 * @param string $key Entry key.
	 */
	private function enqueue_preloads( string $key ): void {
		$file     = $this->get_entry( $key );
		$manifest = $this->get_manifest();
		$imports  = $manifest[ $file ]['imports'] ?? [];

		if ( empty( $imports ) ) {
			return;
		}

		$urls = array_filter( array_map(
			fn( $chunk ) => isset( $manifest[ $chunk ]['file'] )
				? $this->dist_url . $manifest[ $chunk ]['file']
				: null,
			$imports
		));

		if ( empty( $urls ) ) {
			return;
		}

		$hook = is_admin() ? 'admin_head' : 'wp_head';
		add_action( $hook, function () use ( $urls ) {
			foreach ( $urls as $url ) {
				printf( '<link rel="modulepreload" href="%s">' . PHP_EOL, esc_url( $url ) );
			}
		});
	}

	/**
	 * Show a one-time admin notice when manifest.json is missing or unreadable.
	 */
	private function maybe_show_manifest_notice(): void {
		if ( $this->did_manifest_notice ) {
			return;
		}

		$this->did_manifest_notice = true;

		add_action( 'admin_notices', function () {
			printf(
				'<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
				esc_html__( 'Vite manifest.json is missing. Run "ddev npm run build" to generate it.', 'wp-starter' )
			);
		});
	}
}
