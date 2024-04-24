<?php
/**
 * Block: Video Embed
 *
 * @global array $block
 *
 * @package WPStarter
 */

$block_template = [
	[
		'core/pattern',
		[
			'slug' => 'wp-starter/video-embed-inner-blocks',
		],
	],
];
$allowed        = [
	'core/heading',
	'core/paragraph',
	'core/embed'
];
$inner          = [
	'template'      => $block_template,
	'allowedBlocks' => $allowed,
];
?>
<section <?php block_attrs( $block ); ?>>
	<?php inner_blocks( $inner ); ?>
</section>
