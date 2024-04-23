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
	<?php inner_blocks( $inner ); ?>
</section>
