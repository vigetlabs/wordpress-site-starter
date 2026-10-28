<?php
/**
 * Block: Navigation Container
 *
 * @global array $block
 *
 * @package WPStarter
 */

$allowed = [ // phpcs:ignore
	'acf/navigation-toggle',
	'acf/navigation-content',
	'core/group',
	'core/site-logo',
	'core/search',
];
$inner   = [ // phpcs:ignore
	'template'      => $block['template'] ?? [],
	'allowedBlocks' => $allowed,
];
$attrs   = []; // phpcs:ignore

if ( ! is_admin() ) {
	$attrs['x-data']          = '{menuIsOpen : false}'; // phpcs:ignore
	$attrs['x-id']            = "['navigation-content']"; // phpcs:ignore
	$attrs['x-trap']          = 'menuIsOpen'; // phpcs:ignore
	$attrs['@keydown.escape'] = 'menuIsOpen = false'; // phpcs:ignore
}

?>
<div <?php block_attrs( $block, '', $attrs ); ?>>
	<?php inner_blocks( $inner ); ?>
</div>
