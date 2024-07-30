<?php
/**
 * Block: Text & Icon Cards
 *
 * @global array $block
 *
 * @package WPStarter
 */

$block_template = [
	[ 'acf/text-icon-card' ],
	[ 'acf/text-icon-card' ],
	[ 'acf/text-icon-card' ],
];
$allowed        = [ 'acf/text-icon-card' ];
$inner          = [
	'template'      => $block_template,
	'allowedBlocks' => $allowed,
];
?>
<section <?php block_attrs( $block ); ?>>
	<div class="alignwide">
		<?php inner_blocks( $inner ); ?>
	</div>
</section>
