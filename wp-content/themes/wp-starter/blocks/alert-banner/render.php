<?php
/**
 * Block: Alert Banner
 *
 * @global array $block
 *
 * @package WPStarter
 */

$block_template = [
	[
		'core/paragraph',
		[
			'placeholder' => __( 'Enter alert banner message...', 'wp-starter' ),
		],
	],
];
$allowed        = [
	'core/paragraph',
	'core/button',
];
$inner          = [
	'template'      => $block_template,
	'allowedBlocks' => $allowed,
];
?>
<section <?php block_attrs( $block, "gap-24 lg:gap-48" ); ?>>
	<div class="flex flex-col gap-24 items-start lg:flex-row lg:gap-48 lg:items-center"><?php inner_blocks( $inner ); ?></div>
	<?php alert_banner_dismiss_button(); ?>
</section>
