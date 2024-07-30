<?php
/**
 * Block: Video Embed
 *
 * @global array $block
 *
 * @package WPStarter
 */

$block_template = [
	[
		'core/heading',
		[
			'textAlign' => 'center',
		],
	],
	[
		'core/paragraph',
		[
			'align' => 'center',
		],
	],
	[
		'core/embed',
		[
			'align' => 'center',
		],
	],
];
$allowed        = [
	'core/heading',
	'core/paragraph',
	'core/embed',
];
$inner          = [
	'template'      => $block_template,
	'allowedBlocks' => $allowed,
];
?>
<section <?php block_attrs( $block ); ?>>
	<div class="alignwide">
		<?php inner_blocks( $inner ); ?>
	</div>
</section>
