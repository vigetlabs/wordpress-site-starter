<?php
/**
 * Block: Text & Icon Card
 *
 * @global array $block
 *
 * @package WPStarter
 */

$block_template = [
	[
		'core/pattern',
		[
			'slug' => 'wp-starter/text-icon-card-inner-blocks',
		],
	],
];
$inner = [
	'template' => $block_template,
];
?>
<article <?php block_attrs( $block ); ?>>
	<?php inner_blocks( $inner ); ?>
</article>
