<?php
/**
 * Motion: reads motion.json (theme root + per-block) and tags rendered blocks with data-motion attributes.
 *
 * @package WPStarter
 */

namespace WPStarter;

/**
 * Motion class.
 */
class Motion {

	/**
	 * Singleton instance.
	 *
	 * @var ?Motion
	 */
	private static ?Motion $instance = null;

	/**
	 * Theme-level motion.json.
	 *
	 * @var array
	 */
	private array $config = [];

	/**
	 * Per-block motion.json files, keyed by block name.
	 *
	 * @var array<string, array>
	 */
	private array $block_rules = [];

	/**
	 * Per-block `supports.motion` values, keyed by block name.
	 *
	 * @var array<string, bool>
	 */
	private array $supports = [];

	/**
	 * Render stack of motion frames (pushed on render_block_data, popped on render_block).
	 *
	 * @var array
	 */
	private array $stack = [];

	/**
	 * Return the singleton instance.
	 *
	 * @return Motion
	 */
	public static function get_instance(): Motion {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Load config and register hooks.
	 */
	private function __construct() {
		$this->config = self::read_json( get_stylesheet_directory() . '/motion.json' );

		// Block motion.json files load as blocks register, so they work from any VBT block location.
		add_filter( 'block_type_metadata_settings', [ $this, 'collect_block_rules' ], 10, 2 );

		if ( empty( $this->config['enabled'] ) ) {
			return;
		}

		add_action( 'wp_head', [ $this, 'print_head' ], 1 );
		add_filter( 'render_block_data', [ $this, 'push_frame' ], 10, 3 );
		add_filter( 'render_block', [ $this, 'pop_frame' ], 10, 2 );
	}

	/**
	 * Read a JSON file as an array.
	 *
	 * @param string $file Path.
	 *
	 * @return array
	 */
	private static function read_json( string $file ): array {
		if ( ! file_exists( $file ) ) {
			return [];
		}
		return (array) wp_json_file_decode( $file, [ 'associative' => true ] );
	}

	/**
	 * Store a block's motion.json and supports.motion when it registers.
	 *
	 * @param array $settings Block settings.
	 * @param array $metadata Block metadata from block.json.
	 *
	 * @return array
	 */
	public function collect_block_rules( array $settings, array $metadata ): array {
		if ( empty( $metadata['name'] ) || empty( $metadata['file'] ) ) {
			return $settings;
		}

		$name = str_contains( $metadata['name'], '/' ) ? $metadata['name'] : 'acf/' . $metadata['name'];

		if ( isset( $metadata['supports']['motion'] ) ) {
			$this->supports[ $name ] = (bool) $metadata['supports']['motion'];
		}

		$rules = self::read_json( dirname( $metadata['file'] ) . '/motion.json' );
		if ( $rules ) {
			$this->block_rules[ $name ] = $rules;
		}

		return $settings;
	}

	/**
	 * Resolve a block's motion frame and push it onto the stack.
	 *
	 * @param array     $parsed_block Parsed block.
	 * @param array     $source_block Unfiltered parsed block.
	 * @param ?\WP_Block $parent_block Parent block, if any.
	 *
	 * @return array
	 */
	public function push_frame( array $parsed_block, array $source_block, ?\WP_Block $parent_block = null ): array {
		$name = $parsed_block['blockName'] ?? null;
		if ( ! $name || ( is_admin() && ! wp_doing_ajax() ) ) {
			return $parsed_block;
		}

		$parent = end( $this->stack ) ?: null;
		$frame  = [
			'id'       => count( $this->stack ) . ':' . wp_unique_id(),
			'off'      => (bool) ( $parent['off'] ?? false ),
			'scope'    => false,
			'rule'     => [],
			'index'    => 0,
			'count'    => 0,
			'animated' => (bool) ( $parent['animated'] ?? false ),
		];

		$theme_rule = $this->config['blocks'][ $name ] ?? null;
		$block_rule = $this->block_rules[ $name ] ?? null;
		$supports   = $this->supports[ $name ] ?? null;

		if ( false === $theme_rule || false === $supports ) {
			$frame['off'] = true;
		}

		if ( ! $frame['off'] ) {
			// Global rules only apply when no ancestor in this scope is already animating.
			$rule = is_array( $theme_rule ) && ! $frame['animated'] ? $theme_rule : [];

			if ( $block_rule ) {
				$rule = array_merge( $rule, array_diff_key( $block_rule, [ 'children' => true ] ) );
			}

			// Scope `children` overrides, outermost first so the nearest scope wins.
			foreach ( $this->stack as $ancestor ) {
				$children = $ancestor['scope'] ? ( $this->block_rules[ $ancestor['name'] ]['children'] ?? [] ) : [];
				$rule     = array_merge( $rule, (array) ( $children['*'] ?? [] ), (array) ( $children[ $name ] ?? [] ) );
			}

			$frame['rule']  = $rule;
			$frame['scope'] = true === $supports || ! empty( $rule['scope'] ) || null !== $block_rule;

			if ( ! empty( $rule['effect'] ) && 'none' !== $rule['effect'] ) {
				$scope_key = $this->nearest_scope_key();
				if ( null !== $scope_key ) {
					$frame['index'] = $this->stack[ $scope_key ]['count']++;
				}
				$frame['animated'] = true;
			} elseif ( $frame['scope'] ) {
				$frame['animated'] = false;
			}
		}

		$frame['name']                = $name;
		$this->stack[]                = $frame;
		$parsed_block['motionFrame'] = $frame['id'];

		return $parsed_block;
	}

	/**
	 * Index of the nearest scope frame on the stack.
	 *
	 * @return ?int
	 */
	private function nearest_scope_key(): ?int {
		for ( $i = count( $this->stack ) - 1; $i >= 0; $i-- ) {
			if ( $this->stack[ $i ]['scope'] ) {
				return $i;
			}
		}
		return null;
	}

	/**
	 * Pop the block's frame and write its data-motion attributes.
	 *
	 * @param string $block_content Rendered block.
	 * @param array  $block         Parsed block.
	 *
	 * @return string
	 */
	public function pop_frame( string $block_content, array $block ): string {
		if ( empty( $block['motionFrame'] ) ) {
			return $block_content;
		}

		// Parsed blocks can be re-rendered without render_block_data (e.g. navigation menus), carrying a stale frame id.
		$key = array_search( $block['motionFrame'], array_column( $this->stack, 'id' ), true );
		if ( false === $key ) {
			return $block_content;
		}

		$frame       = $this->stack[ $key ];
		$this->stack = array_slice( $this->stack, 0, $key );

		if ( $frame['off'] || '' === trim( $block_content ) ) {
			return $block_content;
		}

		$rule   = $frame['rule'];
		$effect = $rule['effect'] ?? 'none';

		if ( 'none' === $effect && ! $frame['scope'] ) {
			return $block_content;
		}

		$tags = new \WP_HTML_Tag_Processor( $block_content );
		if ( ! $tags->next_tag() ) {
			return $block_content;
		}

		$vars = [];
		foreach ( [ 'duration', 'easing', 'distance', 'stagger', 'delay' ] as $prop ) {
			if ( isset( $rule[ $prop ] ) ) {
				$vars[] = sprintf( '--motion-%s:%s', $prop, $this->token( $prop, (string) $rule[ $prop ] ) );
			}
		}

		if ( 'none' !== $effect ) {
			$tags->set_attribute( 'data-motion', $effect );
			if ( $frame['index'] ) {
				$vars[] = '--motion-index:' . $frame['index'];
			}
		}

		if ( $frame['scope'] ) {
			$tags->set_attribute( 'data-motion-scope', '' );
		}

		if ( $vars ) {
			$style = rtrim( trim( (string) $tags->get_attribute( 'style' ) ), ';' );
			$tags->set_attribute( 'style', ltrim( $style . ';' . implode( ';', $vars ), ';' ) );
		}

		return $tags->get_updated_html();
	}

	/**
	 * Resolve a token name to its CSS var, or pass a raw value through.
	 *
	 * @param string $prop  Property (duration, easing, ...).
	 * @param string $value Token name or raw CSS value.
	 *
	 * @return string
	 */
	private function token( string $prop, string $value ): string {
		$group = in_array( $prop, [ 'stagger', 'delay' ], true ) ? 'duration' : $prop;

		if ( isset( $this->config['tokens'][ $group ][ $value ] ) ) {
			return sprintf( 'var(--motion-%s-%s)', $group, $value );
		}

		return self::sanitize_value( $value );
	}

	/**
	 * Strip anything that isn't a plain CSS value character.
	 *
	 * @param string $value Raw value.
	 *
	 * @return string
	 */
	private static function sanitize_value( string $value ): string {
		return (string) preg_replace( '/[^a-zA-Z0-9.,()%\-\s]/', '', $value );
	}

	/**
	 * Print token vars, the page-transition opt-in, and the pre-paint motion class.
	 *
	 * @return void
	 */
	public function print_head(): void {
		$vars = [];
		foreach ( (array) ( $this->config['tokens'] ?? [] ) as $group => $values ) {
			foreach ( (array) $values as $key => $value ) {
				$vars[] = sprintf( '--motion-%s-%s:%s', sanitize_key( $group ), sanitize_key( $key ), self::sanitize_value( (string) $value ) );
			}
		}

		foreach ( (array) ( $this->config['defaults'] ?? [] ) as $prop => $value ) {
			$vars[] = sprintf( '--motion-%s:%s', sanitize_key( $prop ), $this->token( $prop, (string) $value ) );
		}

		$reveal   = (array) ( $this->config['reveal'] ?? [] );
		$vars[]   = '--motion-stagger-limit:' . absint( $reveal['staggerLimit'] ?? 8 );
		$page     = (array) ( $this->config['pageTransition'] ?? [] );
		$page_css = '';

		if ( ! empty( $page['enabled'] ) ) {
			$effect   = sanitize_key( $page['effect'] ?? 'fade' );
			$vars[]   = "--motion-page-out:motion-page-{$effect}-out";
			$vars[]   = "--motion-page-in:motion-page-{$effect}-in";
			$vars[]   = '--motion-page-duration:' . $this->token( 'duration', (string) ( $page['duration'] ?? 'fast' ) );
			$vars[]   = '--motion-page-easing:' . $this->token( 'easing', (string) ( $page['easing'] ?? 'standard' ) );
			$page_css = '@media (prefers-reduced-motion:no-preference){@view-transition{navigation:auto}}';
		}

		$js_config = [
			'threshold'     => (float) ( $reveal['threshold'] ?? 0.15 ),
			'rootMargin'    => (string) ( $reveal['rootMargin'] ?? '0px' ),
			'once'          => (bool) ( $reveal['once'] ?? true ),
			'reducedMotion' => (string) ( $reveal['reducedMotion'] ?? 'none' ),
		];
		?>
		<style id="motion-tokens">:root{<?php echo esc_html( implode( ';', $vars ) ); ?>}<?php echo $page_css; // phpcs:ignore ?></style>
		<script id="motion-init">
			(function (c, h) {
				window.motionConfig = c;
				var reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
				if (reduced && c.reducedMotion === 'none') return;
				h.classList.add('has-motion');
				if (reduced) h.classList.add('motion-reduced');
				// Never leave content hidden if the motion script fails to boot.
				setTimeout(function () { if (!window.motionReady) h.classList.remove('has-motion'); }, 3000);
			})(<?php echo wp_json_encode( $js_config ); ?>, document.documentElement);
		</script>
		<?php
	}
}
