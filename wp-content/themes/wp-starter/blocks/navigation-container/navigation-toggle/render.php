<?php
/**
 * Block: Navigation Toggle
 *
 * @global array $block
 *
 * @package WPStarter
 */

$attrs = [ // phpcs:ignore
	'type' => 'button',
];

if ( ! is_admin() ) {
	$attrs['x-ref']          = 'menuToggle'; // phpcs:ignore
	$attrs['aria-expanded']  = 'false'; // phpcs:ignore
	$attrs[':aria-expanded'] = 'menuIsOpen.toString()'; // phpcs:ignore
	$attrs[':aria-controls'] = "\$id('navigation-content')"; // phpcs:ignore
	$attrs['@click']         = 'menuIsOpen = !menuIsOpen; $el.focus()'; // phpcs:ignore
}

?>
<button <?php block_attrs( $block, '', $attrs ); ?>>
	<span class="sr-only"><?php esc_html_e( 'Menu', 'wp-starter' ); ?></span>
	<svg
		<?php if ( ! is_admin() ) : ?>
			x-show="!menuIsOpen"
		<?php endif; ?>
		class="m-2 menu-open" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" width="27" height="18" viewBox="0 0 27 18" fill="currentColor">
		<path d="M26.8059 1H0.805908" stroke="currentColor" stroke-width="1.5"></path>
		<path d="M26.8059 17H0.805908" stroke="currentColor" stroke-width="1.5"></path>
		<path d="M26.8059 9H0.805908" stroke="currentColor" stroke-width="1.5"></path>
	</svg>
	<?php if ( ! is_admin() ) : ?>
		<svg x-show="menuIsOpen" x-cloak class="m-2 menu-close" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" width="21" height="20" fill="currentColor">
			<path d="M19.192 19.192.808.808M1 19.192 19.385.808" stroke="currentColor" stroke-width="2"></path>
		</svg>
	<?php endif; ?>
</button>
