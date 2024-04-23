<?php
/**
 * Block: CTA
 *
 * @global array $block
 *
 * @package WPStarter
 */

$block_template = [
	[
		'core/pattern',
		[
			'slug' => 'wp-starter/cta-inner-blocks',
		],
	],
];
$inner          = [
	'template' => $block_template,
];
?>
<section <?php block_attrs( $block, 'flex flex-col gap-24 mx-auto p-24 lg:p-64' ); ?>>
	<div class="flex flex-col gap-8 flex-1">
		<?php inner_blocks( $inner ); ?>
	</div>
</section>
