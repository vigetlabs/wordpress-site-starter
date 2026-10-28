<?php
/**
 * Block: Navigation Content
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
	$attrs['x-show']         = 'menuIsOpen'; // phpcs:ignore
	$attrs[':id']            = "\$id('navigation-content')"; // phpcs:ignore
	$attrs[':class']         = "{ 'menu-hidden': !menuIsOpen }"; // phpcs:ignore
	$attrs['@click.outside'] = 'menuIsOpen = false'; // phpcs:ignore
}

?>
<div <?php block_attrs( $block, 'navigation-content', $attrs ); ?>>
	<?php inner_blocks( $inner ); ?>
</div>
