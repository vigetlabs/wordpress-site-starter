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
	 * @param array $vars
	 *
	 * @return array
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
			'template_redirect',
			function () {
				$block = urldecode( get_query_var( self::URL_SLUG ) );

				if ( ! $block || 1 === intval( $block ) ) {
					return;
				}

				header( 'Access-Control-Allow-Origin: *' );
				header( 'content-type: text/html' );

				$output = apply_filters( 'viget_parts_kit_%', '[ Missing Template ]', $block );
				echo apply_filters( 'viget_parts_kit_' . $block, $output );

				exit;
			},
			8
		);
	}

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
