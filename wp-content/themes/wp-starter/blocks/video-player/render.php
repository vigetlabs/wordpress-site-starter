<?php
/**
 * Block: Video Player
 *
 * @global array $block
 *
 * @package WPStarter
 */

$attrs = [];
$inner = [
	'template' => $block['template'] ?? [],
];

if ( ! is_admin() )  {
	$attrs['x-data'] = 'playvideo';
}
?>
<section <?php block_attrs( $block, '', $attrs ); ?>>
	<?php inner_blocks( $inner ); ?>
</section>
