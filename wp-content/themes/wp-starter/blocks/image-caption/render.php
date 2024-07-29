<?php
/**
 * Block: Image with Caption
 *
 * @global array $block
 *
 * @package WPStarter
 */

$block_template = [
	[ 'core/image' ],
];
$allowed        = [ 'core/image' ];
$inner          = [
	'template'      => $block_template,
	'allowedBlocks' => $allowed,
	'templateLock'  => 'all',
];
?>
<section <?php block_attrs( $block ); ?>>
	<div class="alignwide">
		<?php inner_blocks( $inner ); ?>
	</div>
</section>
