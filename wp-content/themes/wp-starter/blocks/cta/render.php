<?php
/**
 * CTA (Dynamic) Block Template.
 *
 * @global array $block
 *
 * @package ACFBlocksToolkit
 */

$block_template = [
	[
		'core/pattern',
		[
			'slug' => 'wp-starter/cta-dynamic',
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
