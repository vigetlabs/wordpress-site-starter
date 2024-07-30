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
		'core/heading',
		[
			'textAlign'   => 'center',
			'placeholder' => 'Headline Goes Here',
		],
	],
	[
		'core/paragraph',
		[
			'align'       => 'center',
			'placeholder' => 'Body text goes here. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat',
		],
	],
	[
		'core/button',
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
