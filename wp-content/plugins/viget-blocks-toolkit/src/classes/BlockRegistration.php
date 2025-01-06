<?php
/**
 * ACF Block Registration
 *
 * @package VigetBlocksToolkit
 */

namespace Viget\VigetBlocksToolkit;

use Timber\Timber;
use WP_Block;

/**
 * Block Registration Class
 */
class BlockRegistration {

	/**
	 * Keep track of all block IDs
	 */
	const BLOCK_IDS_TRANSIENT = 'vgtbt_block_ids';

	/**
	 * @var array
	 */
	public static array $blocks = [];

	/**
	 * Local cache of block IDs.
	 *
	 * @var array
	 */
	public static array $block_ids = [];

	/**
	 * Register ACF Blocks.
	 */
	public static function init(): void {
		// Automate block registration
		self::register_blocks();

		// Set default block render callback for ACF blocks
		self::set_default_callback();

		// Disable inner blocks wrapper
		self::disable_inner_blocks_wrap();

		// Allow for core block style de-registration.
		self::unregister_block_styles();

		// Allow for core block variation de-registration.
		self::unregister_block_variations();

		// Register block patterns within block folders
		self::register_block_patterns();

		// Reset block IDs on a new request
		self::reset_block_ids();

		// Add unique, persistent IDs to each ACF block.
		self::create_block_id();
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
						require_once $include_path;
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

				$metadata['acf']['renderCallback'] = function ( array $block, string $content = '', bool $is_preview = false, int $post_id = 0, ?WP_Block $wp_block = null, array|bool $context = [], bool $is_ajax_render = false ) use ( $metadata ): void {
					$block_name    = str_replace( 'acf/', '', $block['name'] );
					$block['slug'] = sanitize_title( $block_name );
					if ( empty( $block['path'] ) ) {
						$block['path'] = self::get_block_location( $block_name );
					}
					if ( empty( $block['url'] ) ) {
						$block['url'] = self::path_to_url( $block['path'] );
					}

					// Pass the block template data to the block.
					$block['template'] = self::get_inner_blocks( $block, $metadata );

					$twig = $block['path'] . '/render.twig';

					if ( class_exists( '\Timber\Timber' ) && file_exists( $twig ) ) {
						self::render_twig_block( $twig, $block, $content, $is_preview, $post_id, $wp_block, $context, $is_ajax_render );
						return;
					}

					$render = $block['path'] . '/render.php';

					if ( ! file_exists( $render ) ) {
						if ( ! wp_get_current_user() ) {
							return;
						}

						if ( ! empty( $block['supports']['jsx'] ) ) {
							$render = VGTBT_PLUGIN_PATH . '/views/jsx.php';
						} else {
							$render = VGTBT_PLUGIN_PATH . '/views/default.php';
						}
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

		$locations = self::get_block_locations();

		foreach ( $locations as $location ) {
			if ( ! is_dir( $location ) ) {
				continue;
			}

			self::get_blocks_in_dir( $location, self::$blocks );
		}

		return self::$blocks;
	}

	/**
	 * Get locations where custom blocks can be found.
	 *
	 * @return array
	 */
	public static function get_block_locations(): array {
		return array_unique(
			apply_filters(
				'vgtbt_block_locations',
				[
					get_template_directory() . '/blocks',
					get_stylesheet_directory() . '/blocks',
					self::get_custom_blocks_dir(),
				]
			)
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
	 * Get inner blocks template
	 *
	 * @param array $block
	 * @param array $metadata
	 *
	 * @return array
	 */
	public static function get_inner_blocks( array $block, array $metadata = [] ): array {
		if ( ! empty( $metadata['acf']['template'] ) ) {
			return $metadata['acf']['template'];
		} elseif ( ! empty( $metadata['acf']['innerBlocks'] ) ) {
			return $metadata['acf']['innerBlocks'];
		}

		$json_path = $block['path'] . '/template.json';

		if ( ! file_exists( $json_path ) ) {
			return [];
		}

		$json = json_decode( file_get_contents( $json_path ), true );

		if ( empty( $json['template'] ) ) {
			return [];
		}

		return $json['template'];
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

	/**
	 * Disable inner blocks wrap
	 *
	 * @return void
	 */
	private static function disable_inner_blocks_wrap(): void {
		add_filter(
			'acf/blocks/wrap_frontend_innerblocks',
			function ( bool $wrap, string $name ): bool {
				if ( ! str_starts_with( $name, 'acf/' ) ) {
					return $wrap;
				}

				return false;
			},
			10,
			2
		);
	}

	/**
	 * Render Twig block
	 *
	 * @param string     $template
	 * @param array      $block
	 * @param string     $content
	 * @param bool       $is_preview
	 * @param int        $post_id
	 * @param ?WP_Block  $wp_block
	 * @param array|bool $block_context
	 * @param bool       $is_ajax_render
	 *
	 * @return void
	 */
	public static function render_twig_block( string $template, array $block = [], string $content = '', bool $is_preview = false, int $post_id = 0, ?WP_Block $wp_block = null, array|bool $block_context = [], bool $is_ajax_render = false ): void {
		$context = get_queried_object() ? Timber::context() : [];

		// Add additional context to the block.
		$additional = [
			'fields'         => get_fields(),
			'block'          => $block,
			'content'        => $content,
			'is_preview'     => $is_preview,
			'post_id'        => $post_id,
			'wp_block'       => $wp_block,
			'context'        => $block_context,
			'is_ajax_render' => $is_ajax_render,
		];

		$context = array_merge( $context, $additional );

		// Render the block.
		Timber::render( $template, $context );
	}

	/**
	 * Register Custom Block Patterns
	 *
	 * @return void
	 */
	public static function register_block_patterns(): void {
		add_action(
			'init',
			function () {
				$blocks      = self::get_all_blocks();
				$registry    = \WP_Block_Patterns_Registry::get_instance();
				$text_domain = wp_get_theme()->get( 'TextDomain' );

				$default_headers     = [
					'title'         => 'Title',
					'slug'          => 'Slug',
					'description'   => 'Description',
					'viewportWidth' => 'Viewport Width',
					'inserter'      => 'Inserter',
					'categories'    => 'Categories',
					'keywords'      => 'Keywords',
					'blockTypes'    => 'Block Types',
					'postTypes'     => 'Post Types',
					'templateTypes' => 'Template Types',
				];
				$properties_to_parse = [
					'categories',
					'keywords',
					'blockTypes',
					'postTypes',
					'templateTypes',
				];

				foreach ( $blocks as $block ) {
					$patterns = glob( $block['path'] . '/patterns/*.php' );

					if ( empty( $patterns ) ) {
						continue;
					}

					foreach ( $patterns as $pattern_path ) {
						$pattern = get_file_data( $pattern_path, $default_headers );

						if ( $registry->is_registered( $pattern['slug'] ) ) {
							continue;
						}

						foreach ( $properties_to_parse as $property ) {
							if ( ! empty( $pattern[ $property ] ) ) {
								$pattern[ $property ] = array_filter( wp_parse_list( (string) $pattern[ $property ] ) );
							} else {
								unset( $pattern[ $property ] );
							}
						}

						// Parse properties of type int.
						$property = 'viewportWidth';
						if ( ! empty( $pattern[ $property ] ) ) {
							$pattern[ $property ] = (int) $pattern[ $property ];
						} else {
							unset( $pattern[ $property ] );
						}

						// Parse properties of type bool.
						$property = 'inserter';
						if ( ! empty( $pattern[ $property ] ) ) {
							$pattern[ $property ] = in_array(
								strtolower( $pattern[ $property ] ),
								[ 'yes', 'true' ],
								true
							);
						} else {
							unset( $pattern[ $property ] );
						}

						$pattern['filePath'] = $pattern_path;
						$pattern['title']    = translate_with_gettext_context( $pattern['title'], 'Pattern title', $text_domain );
						if ( ! empty( $pattern['description'] ) ) {
							$pattern['description'] = translate_with_gettext_context( $pattern['description'], 'Pattern description', $text_domain );
						}

						register_block_pattern( $pattern['slug'], $pattern );
					}
				}
			},
			11
		);
	}

	/**
	 * Generate a unique block ID for each ACF block
	 *
	 * @return void
	 */
	public static function create_block_id(): void {
		add_filter(
			'acf/pre_save_block',
			function ( array $attributes ): array {
				$wp_block = \WP_Block_Type_Registry::get_instance()->get_registered( $attributes['name'] );
				if ( ! str_starts_with( $attributes['name'], 'acf/' ) || empty( $wp_block?->attributes['blockId'] ) ) {
					return $attributes;
				}

				if ( empty( $attributes['blockId'] ) ) {
					$attributes['blockId'] = uniqid();
				} else {
					// Ensure the block ID is unique.
					while ( self::block_id_exists( $attributes['blockId'] ) ) {
						$attributes['blockId'] = uniqid();
					}
				}

				self::store_block_id( $attributes['blockId'] );

				return $attributes;
			}
		);
	}

	/**
	 * Store a block ID to check for duplicates
	 *
	 * @param string $block_id
	 *
	 * @return void
	 */
	public static function store_block_id( string $block_id ): void {
		self::load_block_ids();

		if ( ! in_array( $block_id, self::$block_ids, true ) ) {
			self::$block_ids[] = $block_id;
			set_transient( self::BLOCK_IDS_TRANSIENT, self::$block_ids, 5 );
		}
	}

	/**
	 * Check if a block ID already exists
	 *
	 * @param string $block_id
	 *
	 * @return bool
	 */
	public static function block_id_exists( string $block_id ): bool {
		self::load_block_ids();
		return in_array( $block_id, self::$block_ids, true );
	}

	/**
	 * Load block IDs from transient
	 *
	 * @return void
	 */
	private static function load_block_ids(): void {
		if ( empty( self::$block_ids ) ) {
			$transient = get_transient( self::BLOCK_IDS_TRANSIENT );
			if ( $transient ) {
				self::$block_ids = $transient;
			}
		}
	}

	/**
	 * Reset the block IDs on a new request.
	 *
	 * @return void
	 */
	private static function reset_block_ids(): void {
		add_action(
			'acf/init',
			function () {
				if ( empty( self::$block_ids ) ) {
					delete_transient( self::BLOCK_IDS_TRANSIENT );
				}
			}
		);
	}

	/**
	 * Allow for core block style de-registration.
	 *
	 * @return void
	 */
	private static function unregister_block_styles(): void {
		add_action(
			'enqueue_block_assets',
			function () {
				$unregister_styles = apply_filters( 'vgtbt_unregister_block_styles', [] );

				wp_localize_script(
					'vgtbt-editor-scripts',
					'vgtbtStyles',
					[
						'unregister' => $unregister_styles,
					]
				);
			},
			20
		);
	}

	/**
	 * Allow for core block variation de-registration.
	 *
	 * @return void
	 */
	private static function unregister_block_variations(): void {
		add_action(
			'enqueue_block_assets',
			function () {
				$unregister_variations = apply_filters( 'vgtbt_unregister_block_variations', [] );

				wp_localize_script(
					'vgtbt-editor-scripts',
					'vgtbtVariations',
					[
						'unregister' => $unregister_variations,
					]
				);
			},
			20
		);
	}
}
