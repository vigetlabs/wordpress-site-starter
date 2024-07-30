<?php
/**
 * Block: Page Header
 *
 * @global array $block
 *
 * @package WPStarter
 */

$inner = [
	'template' => $block['template'] ?? [],
];
?>
<section <?php block_attrs( $block ); ?>>
	<?php inner_blocks( $inner ); ?>
</section>
