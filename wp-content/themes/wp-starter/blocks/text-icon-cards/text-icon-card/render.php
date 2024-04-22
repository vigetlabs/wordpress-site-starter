<?php
/**
 * Block: Text & Icon Cards
 *
 * @global array $block
 *
 * @package WPStarter
 */

$block_template = [
	[
		'core/pattern',
		[
			'slug' => 'wp-starter/text-icon-cards-inner-blocks',
		],
	],
];
$inner = [
	'template' => $block_template,
];
?>
<section <?php block_attrs( $block ); ?>>
	<?php inner_blocks( $inner ); ?>
</section>
