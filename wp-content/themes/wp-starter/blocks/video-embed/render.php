<?php
/**
 * Block: Video Embed
 *
 * @global array $block
 *
 * @package WPStarter
 */

$allowed = [
	'core/heading',
	'core/paragraph',
	'core/embed',
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
