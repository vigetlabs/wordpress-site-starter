<?php
/**
 * ACF Block Registration
 *
 * @package ACFBlocksToolkit
 */

namespace Viget\ACFBlocksToolkit;

/**
 * Block Registration Class
 */
class Block_Registration {

	/**
	 * @var string
	 */
//	const ALL_BLOCKS_TRANSIENT = 'acfbt_all_blocks';

	/**
	 * @var array
	 */
	public static array $blocks = [];

	/**
	 * Register ACF Blocks.
	 */
	public static function init(): void {
		self::register_blocks();
		self::set_default_callback();
	}

	/**
	 * Register the Theme blocks
	 *
	 * @return void
	 */
	public static function register_blocks(): void {
		add_action(
			'acf/init',
			function () {
				$blocks = self::get_all_blocks();

				foreach ( $blocks as $block ) {
					$include_path = $block['path'] . '/block.php';

					// Autoload block.php within block directory
					if ( file_exists( $include_path ) ) {
						require $include_path;
					}

					register_block_type( $block['path'] . '/block.json' );
				}
			}
		);
	}

	/**
	 * Automatically adds block render callbacks.
	 *
	 * @return void
	 */
	public static function set_default_callback(): void {
		add_filter(
			'block_type_metadata',
			function ( array $metadata ): array {
				if ( ! function_exists( 'acf_is_acf_block_json' ) || ! \acf_is_acf_block_json( $metadata ) ) {
					return $metadata;
				}

				if ( ! empty( $metadata['acf']['renderCallback'] ) || ! empty( $metadata['acf']['renderTemplate'] ) || empty( $metadata['name'] ) ) {
					return $metadata;
				}

				$metadata['acf']['renderCallback'] = function ( array $block ): void {
					$block_name    = str_replace( 'acf/', '', $block['name'] );
					$block['slug'] = sanitize_title( $block_name );
					if ( empty( $block['path'] ) ) {
						$block['path'] = self::get_block_location( $block_name );
					}
					if ( empty( $block['url'] ) ) {
						$block['url'] = self::path_to_url( $block['path'] );
					}
					$render = $block['path'] . '/render.php';

					if ( ! file_exists( $render ) ) {
						if ( ! wp_get_current_user() ) {
							return;
						}

						$render = ACFBT_PLUGIN_PATH . '/views/default.php';
					}

					require $render;
				};

				return $metadata;

			},
			5
		);
	}

	/**
	 * Get All Available Blocks
	 *
	 * @return array
	 */
	public static function get_all_blocks(): array {
		if ( ! empty( self::$blocks ) ) {
			return self::$blocks;
		}

//		$transient = get_transient( self::ALL_BLOCKS_TRANSIENT );
//		if ( $transient ) {
//			self::$blocks = $transient;
//			return $transient;
//		}

		$locations = self::get_block_locations();

		foreach ( $locations as $location ) {
			self::get_blocks_in_dir( $location, self::$blocks );
		}

		// Cache for 30min
//		set_transient( ALL_BLOCKS_TRANSIENT, self::$blocks, MINUTE_IN_SECONDS * 30 );

		return self::$blocks;
	}

	/**
	 * Get locations where custom blocks can be found.
	 *
	 * @return array
	 */
	public static function get_block_locations(): array {
		return apply_filters(
			'acfbt_block_locations',
			[
				get_template_directory() . '/blocks',
				self::get_custom_blocks_dir(),
			]
		);
	}

	/**
	 * Get blocks in directory recursively
	 *
	 * @param string $path  Path to search inside.
	 * @param array  $blocks Passed by reference
	 *
	 * @return void
	 */
	public static function get_blocks_in_dir( string $path, array &$blocks = [] ): void {
		$group = glob( trailingslashit( $path ) . '**/block.json' );

		foreach ( $group as $block_path ) {
			$block = json_decode( file_get_contents( $block_path ), true );

			$block['path'] = dirname( $block_path );
			$block['url']  = self::path_to_url( $block['path'] );

			$blocks[] = $block;

			self::get_blocks_in_dir( $block['path'], $blocks );
		}
	}

	/**
	 * Convert path to URL
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public static function path_to_url( string $path ): string {
		$url = str_replace(
			wp_normalize_path( untrailingslashit( ABSPATH ) ),
			site_url(),
			wp_normalize_path( $path )
		);

		return esc_url_raw( $url );
	}

	/**
	 * Get block array
	 *
	 * @param string $block_name
	 *
	 * @return array|false
	 */
	public static function get_block( string $block_name ): array|false {
		$block_path = self::get_block_location( $block_name, 'json' );

		if ( ! $block_path ) {
			return false;
		}

		$block = json_decode( file_get_contents( $block_path ), true );

		$block['path'] = dirname( $block_path );
		$block['url']  = self::path_to_url( $block['path'] );

		return $block;
	}

	/**
	 * Get path to custom uploaded blocks.
	 *
	 * @return string
	 */
	public static function get_custom_blocks_dir(): string {
		$uploads_dir = wp_upload_dir();

		return $uploads_dir['basedir'] . '/acf-blocks';
	}

	/**
	 * Get path to block by name.
	 *
	 * @param string $block_name
	 * @param string $return
	 *
	 * @return false|string
	 */
	public static function get_block_location( string $block_name, string $return = 'directory' ): false|string {
		if ( str_contains( $block_name, '/' ) && ! str_starts_with( $block_name, 'acf/' ) ) {
			return false;
		}

		$block_name = str_replace( 'acf/', '', $block_name );
		$blocks     = self::get_all_blocks();

		foreach ( $blocks as $block ) {
			if ( $block_name !== $block['name'] ) {
				continue;
			}

			if ( 'json' === $return ) {
				return $block['path'] . '/block.json';
			}

			return $block['path'];
		}

		return false;
	}
}
