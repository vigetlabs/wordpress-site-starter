<?php
/**
 * Block: Logo Grid
 *
 * @global array $block
 *
 * @package WPStarter
 */

$block_template = [
	[
		'core/pattern',
		[
			'slug' => 'wp-starter/logo-grid-inner-blocks',
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
