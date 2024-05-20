<?php
/**
 * Block: Navigation Container
 *
 * @global array $block
 *
 * @package WPStarter
 */

$behavior = get_field( 'mobile_behavior' ) ?: 'collapse';
$state    = get_field( 'default_state' ) ?: 'visible';
$attrs    = [];

?>
<div <?php block_attrs( $block, 'wp-block-group', $attrs ); ?>
	x-data="{dropdown}"
	x-on:keydown.escape.prevent.stop="close($refs.button)"
	x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
	x-id="['dropdown-button']"
>
	<button
		class="navigation-toggle p-1"
		aria-rel="mobile-toggle"
		aria-label="<?php esc_attr_e( 'Toggle menu', 'wp-starter' ); ?>"
	>
		<svg xmlns="http://www.w3.org/2000/svg" width="27" height="18" viewBox="0 0 27 18" fill="currentColor" class="menu-open">
			<path d="M26.8059 1H0.805908" stroke="currentColor" stroke-width="1.5"></path>
			<path d="M26.8059 17H0.805908" stroke="currentColor" stroke-width="1.5"></path>
			<path d="M26.8059 9H0.805908" stroke="currentColor" stroke-width="1.5"></path>
		</svg>
		<svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" fill="currentColor" class="menu-close">
			<path d="M19.192 19.192.808.808M1 19.192 19.385.808" stroke="currentColor" stroke-width="2"></path>
		</svg>
	</button>

	<div class="wp-block-group navigation-content"
		x-ref="panel"
		x-show="open"
		x-transition.origin.top.left
		x-on:click.outside="close($refs.button)"
		:id="$id('dropdown-button')"
	>
		<?php inner_blocks(); ?>
	</div>
</div>
