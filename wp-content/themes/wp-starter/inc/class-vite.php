<?php
/**
 *
 * Inspired from:
 *
 * @source https://github.com/andrefelipe/vite-php-setup
 */

/**
 * This class handles loading Vite assets.
 */
class Vite {

	/**
	 * @var string
	 */
	private string $env;

	/**
	 * @var string
	 */
	private string $site_url;

	/**
	 * @var int
	 */
	private string $port;

	/**
	 * @var string
	 */
	private string $dev_server;

	/**
	 * @var string
	 */
	private string $dist_url;

	/**
	 * @var string
	 */
	private string $dist_path;

	/**
	 * @var array
	 */
	private array $entries = [];

	/**
	 * @var bool
	 */
	private bool $initialized = false;

	/**
	 * Set up vars and hooks
	 */
	public function __construct() {
		$this->site_url   = get_site_url();
		$this->port       = is_ssl() ? getenv( 'VITE_PRIMARY_PORT' ) : getenv( 'VITE_SECONDARY_PORT' );
		$this->dev_server = "{$this->site_url}:{$this->port}";

		$this->dist_url  = get_stylesheet_directory_uri() . '/dist/';
		$this->dist_path = get_stylesheet_directory() . '/dist/.vite/';

		$this->env = getenv( 'ENVIRONMENT' );

		//set frontend css/js
		$this->entries['default'] = 'main.js';

		//set editor css/js
		$this->entries['editor']  = 'main.js';

		add_action( 'wp_head', [ $this, 'init' ] );

		add_action(
			'admin_head',
			function () {
				$screen = get_current_screen();
				if ( $screen->is_block_editor ) {
					// $this->init(); // This breaks the block editor styles.
					$this->init( 'editor' );
				}
			}
		);

		add_filter( 'script_loader_tag', [ $this, 'script_loader' ], 10, 3 );
		add_filter( 'style_loader_tag', [ $this, 'style_loader' ], 10, 4 );
	}

	/**
	 * Initialize
	 *
	 * @param string $entry Entry key.
	 *
	 * @return void
	 */
	public function init( string $entry = '' ): void {
		if ( ! $this->get_entry( $entry ) ) {
			return;
		}

		$this->initialized = true;

		/* Print Vite HTML tags */
		echo $this->vite( $this->get_entry( $entry ) );
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

		if ( array_key_exists( $key, $this->entries ) ) {
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

		if ( array_key_exists( $entry, $this->entries ) ) {
			return $this->entries[ $entry ];
		}

		return false;
	}

	/**
	 * Prints all the html entries needed for Vite
	 *
	 * @param string $entry The entry point file.
	 *
	 * @return string
	 */
	public function vite( string $entry ) {
		if ( 'dev' === $this->env ) {
			$scripts = [];

			if ( ! $this->initialized ) {
				$scripts[] = "<script type=\"module\" src=\"{$this->dev_server}/@vite/client\"></script>";
			}

			$scripts[] = "<script type=\"module\" src=\"{$this->dev_server}/{$entry}\"></script>";

			return implode( PHP_EOL, $scripts );
		}

		/* Will need to be updated to work with vendor files */
		return implode(
			PHP_EOL,
			[
				$this->js( $entry ),
				$this->imports( $entry ),
				$this->css( $entry ),
			]
		);
	}

	/**
	 * Helpers to print tags
	 *
	 * @param string $entry The entry point file.
	 *
	 * @return string
	 */
	private function js( string $entry ): string {
		$url = $this->get_asset_url( $entry );

		if ( ! $url ) {
			return '';
		}

		return sprintf( '<script type="module" crossorigin src="%s"></script>', esc_url( $url ) );
	}

	/**
	 * Helper to print preload URLs.
	 *
	 * @param string $entry The entry point file.
	 *
	 * @return string
	 */
	private function imports( string $entry ): string {
		$res = '';

		foreach ( $this->get_imports( $entry ) as $url ) {
			$res .= sprintf( '<link rel="modulepreload" href="%s">', esc_url( $url ) );
		}

		return $res;
	}

	/**
	 * Adjust theme style tags.
	 *
	 * @param string $entry The entry point file.
	 *
	 * @return string
	 */
	private function css( string $entry ): string {
		$tags = '';

		foreach ( $this->get_css( $entry ) as $url ) {
			$tags .= sprintf( '<link rel="stylesheet" href="%s">', esc_url( $url ) );
		}

		return $tags;
	}

	/**
	 * Helper to locate build files
	 *
	 * @return array
	 */
	private function get_manifest(): array {
		$manifest = $this->dist_path . '/manifest.json';

		if ( ! file_exists( $manifest ) ) {
			add_action(
				'admin_notices',
				function () {
					printf(
						'<div class="notice notice-warning is-dismissible">
						<p>%s</p>
					</div>',
						esc_html__( 'Manifest.json file is missing. Run "ddev restart" to fix.', 'wp-starter' )
					);
				}
			);

			return [];
		}

		$content = file_get_contents( $manifest );

		if ( ! $content ) {
			die( 'Error: The manifest.json file is empty.' );
		}

		return json_decode( $content, true );
	}

	/**
	 * Get Asset URL
	 *
	 * @param string $entry The entry point file.
	 *
	 * @return string
	 */
	private function get_asset_url( string $entry ): string {
		$manifest = $this->get_manifest();

		if ( ! isset( $manifest[ $entry ] ) ) {
			die( "Could not find entry in manifest for $entry" );
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
	private function get_css( string $entry ): array {
		$urls     = [];
		$manifest = $this->get_manifest();

		if ( ! empty( $manifest[ $entry ]['css'] ) ) {
			foreach ( $manifest[ $entry ]['css'] as $file ) {
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
		if ( ! str_contains( $handle, 'theme-script-' ) ) {
			return $tag;
		}

		return sprintf( '<script type="module" crossorigin src="%s"></script>', esc_url( $src ) );
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

		return sprintf(
			'<link rel="modulepreload" href="%s" media="%s">',
			esc_url( $href ),
			esc_attr( $media )
		);
	}

	/**
	 * Enqueue assets in Admin.
	 *
	 * @param string $entry The entry point file.
	 */
	public function admin_assets( $entry = '' ): void {
		if ( ! $entry ) {
			$screen = get_current_screen();
			$entry  = $screen && 'site-editor' === $screen->base ? 'default' : 'editor';
		}

		if ( ! $this->get_entry( $entry ) ) {
			return;
		}

		foreach ( $this->get_css( $this->get_entry( $entry ) ) as $url ) {
			add_editor_style( $url );
			break;
		}
	}

	/**
	 * Enqueue assets for block editor
	 *
	 * @param string $entry The entry point file.
	 */
	public function block_assets( $entry = '' ): void {
		if ( ! $entry ) {
			$screen = get_current_screen();
			$entry  = $screen && 'site-editor' === $screen->base ? 'default' : 'editor';
		}

		if ( ! $this->get_entry( $entry ) ) {
			return;
		}

		$i    = 0;
		$file = $this->get_entry( $entry );

		$css_dependencies = [
			'wp-block-library-theme',
			'wp-block-library',
		];

		foreach ( $this->get_css( $file ) as $url ) {
			++$i;
			wp_enqueue_style( 'theme-style-editor-' . $i, $url, $css_dependencies, '1.0' );
		}

		foreach ( $this->get_imports( $file ) as $url ) {
			++$i;
			wp_enqueue_style( 'theme-style-preload-editor-' . $i, $url, $css_dependencies, '1.0' );
		}

		/* Theme Gutenberg blocks JS. */
		$js_dependencies = [
			'wp-block-editor',
			'wp-blocks',
			'wp-editor',
			'wp-components',
			'wp-compose',
			'wp-data',
			'wp-element',
			'wp-hooks',
			'wp-i18n',
		];

		$script_url = $this->get_asset_url( $file );

		if ( 'dev' === $this->env ) {
			$vite_client = $this->dev_server . '/@vite/client';
			$vite_entry  = $this->dev_server . '/' . $entry;

			wp_register_script(
				'theme-script-editor-vite-client',
				$vite_client,
				$js_dependencies,
				'1.0',
				true
			);

			wp_enqueue_script(
				'theme-script-editor-vite-entry',
				$vite_entry,
				[ 'theme-script-editor-vite-client' ],
				'1.0',
				true
			);
		} else {
			wp_enqueue_script(
				'theme-script-editor-main',
				$script_url,
				$js_dependencies,
				'1.0',
				true
			);
		}
	}
}

new Vite();
