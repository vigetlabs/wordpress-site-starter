<?php
/**
 * Block: Image with Caption
 *
 * @global array $block
 *
 * @package WPStarter
 */

$inner = [
	'template'     => $block['template'] ?? [],
	'templateLock' => 'all',
];
?>
<section <?php block_attrs( $block ); ?>>
	<div class="acf-block-inner__container">
		<?php inner_blocks( $inner ); ?>
	</div>
</section>
