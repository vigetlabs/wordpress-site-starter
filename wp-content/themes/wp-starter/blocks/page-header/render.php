<?php
/**
 * Block: Page Header
 *
 * @global array $block
 *
 * @package WPStarter
 */

$block_template = [
	[
		'acf/breadcrumbs',
	],
	[
		'core/columns',
		[
			'verticalAlignment' => 'center',
		],
		[
			[
				'core/column',
				[
					'verticalAlignment' => 'center',
					'width'             => '66.66%',
				],
				[
					[
						'core/post-title',
					],
					[
						'core/paragraph',
						[
							'placeholder' => 'Page description ponam in culpa idiota aliis pravitatis. Principium ponere culpam in se justum praeceptum. Neque improperes et aliis qui non perfecte ipse docuit.',

						],
					],
				],
			],
			[
				'core/column',
				[
					'verticalAlignment' => 'center',
					'width'             => '33.33%',
				],
				[
					[
						'core/image',
					],
				],

			],
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
