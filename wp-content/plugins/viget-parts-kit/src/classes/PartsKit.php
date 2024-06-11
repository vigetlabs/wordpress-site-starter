<?php
/**
 * Initializes the Parts Kit
 *
 * @package VigetPartsKit
 */

namespace VigetPartsKit;

/**
 * Parts Kit Class
 */
class PartsKit {

	/**
	 * URL slug for the Parts Kit
	 * @var string
	 */
	const URL_SLUG = 'parts-kit';

	/**
	 * Initialize the Parts Kit
	 */
	public function __construct() {
		// Create rewrite rules
		$this->setup_rewrite_rules();

		// Add Query Vars
		$this->add_query_vars();

		// Render Block JSON
		$this->render_json();

		// Render Parts Kit (Root)
		$this->render_parts_kit();

		// Render a Block
		$this->render_block();

		// Insert the Admin Menu page.
		$this->add_admin_menu_page();

		// Open Parts Kit in New Window
		$this->adjust_admin_link_target();
	}

	/**
	 * Create custom URLs:
	 *    parts-kit.json
	 *    parts-kit/
	 *    parts-kit/block-name/
	 *
	 * @return void
	 */
	private function setup_rewrite_rules(): void {
		add_action(
			'init',
			function () {
				add_rewrite_rule(
					preg_quote( self::URL_SLUG . '.json' ) . '$',
					'index.php?' . self::URL_SLUG . '-json=1',
					'top'
				);

				add_rewrite_rule(
					preg_quote( self::URL_SLUG ) . '/([^/]+)/?$',
					'index.php?' . self::URL_SLUG . '=$matches[1]',
					'top'
				);

				add_rewrite_rule(
					preg_quote( self::URL_SLUG ) . '/?$',
					'index.php?' . self::URL_SLUG . '=1',
					'top'
				);
			}
		);
	}

	/**
	 * Set up query vars
	 *
	 * @return void
	 */
	private function add_query_vars(): void {
		add_filter(
			'query_vars',
			function ( array $vars ): array {
				if ( ! in_array( self::URL_SLUG, $vars, true ) ) {
					$vars[] = self::URL_SLUG;
				}

				if ( ! in_array( self::URL_SLUG . '-json', $vars, true ) ) {
					$vars[] = self::URL_SLUG . '-json';
				}

				return $vars;
			}
		);
	}

	/**
	 * Output Parts Kit JSON
	 *
	 * @return void
	 */
	private function render_json(): void {
		add_action(
			'template_redirect',
			function () {
				$is_json = get_query_var( self::URL_SLUG . '-json' );

				if ( ! $is_json ) {
					return;
				}

				/*
				Array Structure:
				[
					[
						'title' => 'Button',
						'url'   => '',
						'children' => [
							[
								'title' => 'Primary',
								'url' => '/button-primary.html',
								'children' => [],
							],
							[
								'title' => 'Secondary',
								'url' => '/button-secondary.html',
								'children' => [],
							],
						],
					],
					[
						'title' => 'Card',
						'url' => '/card.html',
						'children' => [],
					],
				]
				*/

				$parts = array_values( apply_filters( 'viget_parts_kit', [] ) );

				$json = [
					'schemaVersion' => '0.0.1',
					'nav'           => $parts,
				];

				header( 'Access-Control-Allow-Origin: *' );
				header( 'content-type: application/json' );

				echo wp_json_encode( $json );
				exit;
			}
		);
	}

	/**
	 * Output the Parts Kit
	 *
	 * @return void
	 */
	private function render_parts_kit(): void {
		add_action(
			'template_redirect',
			function () {
				$block = get_query_var( self::URL_SLUG );

				if ( ! $block || 1 !== intval( $block ) ) {
					return;
				}

				defined( 'IS_PARTS_KIT' ) || define( 'IS_PARTS_KIT', true );

				printf(
					'<html lang="en">
						<head>
							<title>%s</title>
						</head>
						<body>
							<script type="module" src="https://unpkg.com/@viget/parts-kit@^0/lib/parts-kit.js"></script>
							<parts-kit config-url="%s"></parts-kit>
						</body>
					</html>',
					esc_html__( 'Parts Kit', 'viget-parts-kit' ),
					esc_url( home_url( self::URL_SLUG . '.json' ) )
				);
				exit;
			}
		);
	}

	/**
	 * Output the example block with dummy content.
	 *
	 * @return void
	 */
	private function render_block(): void {
		add_action(
			'init',
			function () {
				if ( empty( $_SERVER['REQUEST_URI'] ) || ! str_starts_with( $_SERVER['REQUEST_URI'], '/' . self::URL_SLUG . '/' ) ) {
					return;
				}

				$gutenberg = new WPGutenberg();
				$gutenberg->load();
			}
		);

		add_action(
			'template_redirect',
			function () {
				$block_name = urldecode( get_query_var( self::URL_SLUG ) );

				if ( ! $block_name || 1 === intval( $block_name ) ) {
					return;
				}

				$this->disable_admin_bar();

				header( 'Access-Control-Allow-Origin: *' );

				defined( 'IS_PARTS_KIT' ) || define( 'IS_PARTS_KIT', true );

				$block = [
					'blockName'    => $block_name,
					'attrs'        => [],
					'innerContent' => [],
				];

				$output = serialize_block( $block );
				$output = apply_filters( 'viget_parts_kit_%', $output, $block_name );
				$output = apply_filters( 'viget_parts_kit_' . $block_name, $output );

				$my_cover1 = '<!-- wp:cover {} /-->';
				$my_cover2 = '<!-- wp:cover {"url":{"type":"string"},"useFeaturedImage":{"type":"boolean","default":false},"id":{"type":"number"},"alt":{"type":"string","default":""},"hasParallax":{"type":"boolean","default":false},"isRepeated":{"type":"boolean","default":false},"dimRatio":{"type":"number","default":100},"overlayColor":{"type":"string"},"customOverlayColor":{"type":"string"},"isUserOverlayColor":{"type":"boolean"},"backgroundType":{"type":"string","default":"image"},"focalPoint":{"type":"object"},"minHeight":{"type":"number"},"minHeightUnit":{"type":"string"},"gradient":{"type":"string"},"customGradient":{"type":"string"},"contentPosition":{"type":"string"},"isDark":{"type":"boolean","default":true},"allowedBlocks":{"type":"array"},"templateLock":{"type":["string","boolean"],"enum":["all","insert","contentOnly",false]},"tagName":{"type":"string","default":"div"},"lock":{"type":"object"},"metadata":{"type":"object"},"align":{"type":"string","enum":["left","center","right","wide","full",""]},"style":{"type":"object"},"borderColor":{"type":"string"},"textColor":{"type":"string"},"className":{"type":"string"},"layout":{"type":"object"},"fontSize":{"type":"string"},"fontFamily":{"type":"string"}} /-->';

				$cover = '<!-- wp:cover {"layout":{"type":"constrained"}} -->
<div class="wp-block-cover"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container"></div></div>
<!-- /wp:cover -->';

				$output = $cover;

				require VPK_PLUGIN_PATH . 'views/editor.php';

				exit;
			},
			12
		);
	}

	/**
	 * Remove the admin bar.
	 *
	 * @return void
	 */
	private function disable_admin_bar(): void {
		add_filter( 'show_admin_bar', '__return_false' );
		remove_action( 'wp_head', 'wp_admin_bar_header' );
		remove_theme_support( 'admin-bar' );
		remove_all_actions( 'admin_bar_init' );
		remove_action( 'wp_body_open', 'wp_admin_bar_render', 0 );
		remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
	}
	/**
	 * Get the block editor settings
	 *
	 * @return array
	 */
	private function get_block_editor_settings(): array {
		$settings = array(
			'disableCustomColors'    => get_theme_support( 'disable-custom-colors' ),
			'disableCustomFontSizes' => get_theme_support( 'disable-custom-font-sizes' ),
			// 'imageSizes'             => $available_image_sizes,
			'isRTL'                  => is_rtl(),
			// 'maxUploadFileSize'      => $max_upload_size,
		);
		list( $color_palette, ) = (array) get_theme_support( 'editor-color-palette' );
		list( $font_sizes, )    = (array) get_theme_support( 'editor-font-sizes' );
		if ( false !== $color_palette ) {
			$settings['colors'] = $color_palette;
		}
		if ( false !== $font_sizes ) {
			$settings['fontSizes'] = $font_sizes;
		}

		return $settings;
	}

	/**
	 * Adjust the admin link target to open in a new window
	 *
	 * @return void
	 */
	private function adjust_admin_link_target(): void {
		add_action(
			'admin_head',
			function () {
				?>
				<script type="text/javascript">
					jQuery( function($) {
						$( "ul#adminmenu a[href$='tools.php?page=<?php echo esc_html( self::URL_SLUG ) ?>']" ).attr( 'target', '_blank' );
					});
				</script>
				<?php
			}
		);
	}

	/**
	 * Insert the Parts Kit Admin Menu page
	 *
	 * @return void
	 */
	private function add_admin_menu_page(): void {
		add_action(
			'admin_menu',
			function () {
				add_submenu_page(
					'tools.php',
					__( 'Parts Kit', 'viget-parts-kit' ),
					__( 'Parts Kit', 'viget-parts-kit' ),
					'edit_posts',
					self::URL_SLUG,
					function() {
						if ( ! headers_sent() ) {
							wp_safe_redirect( home_url( self::URL_SLUG ) );
						}

						echo '<script type="text/javascript">
							window.location.href = "' . esc_url( home_url( self::URL_SLUG ) ) . '";
						</script>';
						echo 'Redirecting...';
						exit;
					}
				);
			}
		);
	}
}
