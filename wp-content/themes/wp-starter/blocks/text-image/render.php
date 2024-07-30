<?php
/**
 * Block: Text & Image
 *
 * @global array $block
 *
 * @package WPStarter
 */

$block_template = [
	[
		'core/media-text',
		[],
		[
			[
				'core/paragraph',
				[
					'className'   => 'is-style-eyebrow',
					'placeholder' => 'Eyebrow Text',
				],
			],
			[
				'core/heading',
				[
					'level'       => 3,
					'placeholder' => 'Headline Text',
				],
			],
			[
				'core/paragraph',
				[
					'placeholder' => 'Body Text',
				],
			],
			[
				'core/buttons',
			],
		],
	],
];
$allowed        = [
	'core/media-text',
	'core/paragraph',
	'core/heading',
	'core/group',
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
