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
<section <?php block_attrs( $block ); ?>>
	<?php alert_banner_dismiss_button(); ?>
	<?php inner_blocks( $inner ); ?>
</section>
