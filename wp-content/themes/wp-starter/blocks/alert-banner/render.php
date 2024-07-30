<?php
/**
 * Block: Alert Banner
 *
 * @global array $block
 *
 * @package WPStarter
 */

$id             = 'alert' . $block['block_id'] ?? $block['id'];
$block_template = [
	[
		'core/paragraph',
		[
			'placeholder' => 'Enter alert banner message...',
		],
	],
	[
		'core/button',
		[
			'className' => 'is-style-outline',
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
$attrs          = [];

if ( ! is_admin() ) {
	$attrs['x-data'] = '{ ' . $id . ': $persist(true) }';
	$attrs['x-show'] = $id;
}
?>
<section <?php block_attrs( $block, '', $attrs ); ?> >
	<div class="wp-block-alert-banner__inner-container">
		<?php inner_blocks( $inner ); ?>
	</div>
	<?php alert_banner_dismiss_button( $id ); ?>
</section>
