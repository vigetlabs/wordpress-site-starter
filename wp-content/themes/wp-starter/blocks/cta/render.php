<?php
/**
 * Block: CTA
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
	<div class="alignwide">
		<?php inner_blocks( $inner ); ?>
	</div>
</section>
