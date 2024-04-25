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
<section
	<?php if ( !is_admin() ): ?>
		x-data="{ <?php echo $block['id']?>: $persist(true) }"
		x-show="<?php echo $block['id']?>"
	<?php endif; ?>
 	<?php block_attrs( $block, "gap-24 lg:gap-48" ); ?>
 >
	<div class="flex flex-col items-start gap-24 lg:flex-row lg:gap-48 lg:items-center"><?php inner_blocks( $inner ); ?></div>
	<?php alert_banner_dismiss_button( $block['id'] ); ?>
</section>
