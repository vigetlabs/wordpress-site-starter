<?php
/**
 * Block: Navigation Container
 *
 * @global array $block
 *
 * @package WPStarter
 */

$allowed = [ // phpcs:ignore
	'core/group',
	'core/paragraph',
	'core/site-logo',
	'core/buttons',
	'core/button',
	'core/navigation',
	'core/search',
	'core/spacer',
	'core/separator',
];
$inner   = [ // phpcs:ignore
	'template'      => $block['template'] ?? [],
	'allowedBlocks' => $allowed,
];
$attrs   = []; // phpcs:ignore

if ( ! is_admin() ) {
	$attrs['x-data'] = '{menuIsOpen : false}'; // phpcs:ignore
	$attrs['x-trap'] = 'menuIsOpen'; // phpcs:ignore
}

?>
<div <?php block_attrs( $block, '', $attrs ); ?>>
	<button
		<?php if ( ! is_admin() ) : ?>
			@click="menuIsOpen = !menuIsOpen"
			x-cloak
			class="navigation-toggle"
		<?php else : ?>
			class="hidden"
		<?php endif; ?>
		aria-rel="mobile-toggle"
	>
		<svg
			<?php if ( ! is_admin() ) : ?>
				x-show="!menuIsOpen"
			<?php endif; ?>
			class="m-2" xmlns="http://www.w3.org/2000/svg" width="27" height="18" viewBox="0 0 27 18" fill="currentColor" class="menu-open">
			<title><?php esc_html_e( 'Open Menu', 'wp-starter' ); ?></title>
			<path d="M26.8059 1H0.805908" stroke="currentColor" stroke-width="1.5"></path>
			<path d="M26.8059 17H0.805908" stroke="currentColor" stroke-width="1.5"></path>
			<path d="M26.8059 9H0.805908" stroke="currentColor" stroke-width="1.5"></path>
		</svg>
		<svg
			<?php if ( ! is_admin() ) : ?>
				x-show="menuIsOpen"
			<?php endif; ?>
			class="m-2" xmlns="http://www.w3.org/2000/svg" width="21" height="20" fill="currentColor" class="menu-close">
			<title><?php esc_html_e( 'Close Menu', 'wp-starter' ); ?></title>
			<path d="M19.192 19.192.808.808M1 19.192 19.385.808" stroke="currentColor" stroke-width="2"></path>
		</svg>
	</button>

	<div
		<?php if ( ! is_admin() ) : ?>
			x-show="menuIsOpen"
			@click.away="menuIsOpen = false"
		<?php endif; ?>
		class="acf-block-inner__container"
	>
		<div class="navigation-content">
			<?php inner_blocks( $inner ); ?>
		</div>
	</div>
</div>
