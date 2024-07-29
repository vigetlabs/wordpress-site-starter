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
<section <?php block_attrs( $block ); ?>>
	<div class="alignwide">
		<?php inner_blocks( $inner ); ?>
	</div>
</section>
