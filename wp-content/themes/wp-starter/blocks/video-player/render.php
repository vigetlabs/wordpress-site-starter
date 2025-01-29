<?php
/**
 * Block: Video Player
 *
 * @global array $block
 *
 * @package WPStarter
 */

$attrs = []; // phpcs:ignore
$inner = [ // phpcs:ignore
	'template'      => $block['template'] ?? [],
	'allowedBlocks' => [
		'core/cover',
		'core/embed',
	],
];

if ( ! is_admin() ) {
	$attrs['x-data'] = 'playvideo'; // phpcs:ignore
}
?>
<section <?php block_attrs( $block, '', $attrs ); ?>>
	<?php inner_blocks( $inner ); ?>
</section>
