<?php
/**
 * Block: Text & Icon Card
 *
 * @global array $block
 *
 * @package WPStarter
 */

$inner = [
	'template' => $block['template'] ?? [],
];
?>
<article <?php block_attrs( $block ); ?>>
	<?php inner_blocks( $inner ); ?>
</article>
