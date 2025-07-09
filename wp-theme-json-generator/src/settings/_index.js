import getColorSettings from './color.js';
import layout from './layout.js';
import spacing from './spacing.js';
import typography from './typography.js';
import blocks from './blocks.js';

export default function getSettings(cssPath = 'src/styles/tailwind.css') {
	return {
		appearanceTools: true,
		useRootPaddingAwareAlignments: true,
		color: getColorSettings(cssPath),
		layout: layout,
		spacing: spacing,
		typography: typography,
		blocks: blocks,
	};
}
