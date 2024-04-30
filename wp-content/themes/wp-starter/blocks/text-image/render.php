<?php
/**
 * Block: Text & Image
 *
 * @global array $block
 *
 * @package WPStarter
 */

$block_template = [
	[
		'core/pattern',
		[
			'slug' => 'wp-starter/text-image-inner-blocks',
		],
	],
];
$allowed        = [
	'core/media-text',
	'core/paragraph',
	'core/heading',
	'core/group',
];
$inner          = [
	'template'      => $block_template,
	'allowedBlocks' => $allowed,
];
?>
<section <?php block_attrs( $block ); ?>>
	<?php inner_blocks( $inner ); ?>
</section>
