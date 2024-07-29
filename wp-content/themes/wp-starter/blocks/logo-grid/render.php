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
	<div class="flex flex-wrap gap-24 justify-left alignwide">
		<?php inner_blocks( $inner ); ?>
	</div>
</section>
