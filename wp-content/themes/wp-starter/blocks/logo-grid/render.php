<?php
/**
 * Block: Logo Grid
 *
 * @global array $block
 *
 * @package WPStarter
 */

$block_template = [
	[ 'core/image' ],
	[ 'core/image' ],
	[ 'core/image' ],
];
$allowed        = [
	'core/image',
];
$inner          = [
	'template'      => $block_template,
	'allowedBlocks' => $allowed,
];
?>
<section <?php block_attrs( $block ); ?>>
	<?php inner_blocks( $inner ); ?>
</section>
