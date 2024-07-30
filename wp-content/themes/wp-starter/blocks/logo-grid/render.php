<?php
/**
 * Block: Logo Grid
 *
 * @global array $block
 *
 * @package WPStarter
 */

$allowed = [
	'core/image',
];
$inner   = [
	'template'      => $block['template'] ?? [],
	'allowedBlocks' => $allowed,
];
?>
<section <?php block_attrs( $block ); ?>>
	<div class="alignwide">
		<?php inner_blocks( $inner ); ?>
	</div>
</section>
