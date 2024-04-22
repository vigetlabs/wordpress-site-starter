<?php
/**
 * Block: Accordion
 *
 * @global array $block
 *
 * @package WPStarter
 */

$block_template = [
	[
		'core/details',
		[
			'showContent' => true,
		],
	],
	[ 'core/details' ],
	[ 'core/details' ],
];
$allowed        = [
	'core/details',
];
$inner          = [
	'template'      => $block_template,
	'allowedBlocks' => $allowed,
];
?>
<section <?php block_attrs( $block ); ?>>
	<?php inner_blocks( $inner ); ?>
</section>
