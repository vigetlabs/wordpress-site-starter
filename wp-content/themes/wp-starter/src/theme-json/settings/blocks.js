// Configure custom block settings for each block to override the default theme settings.
// Merged with values derived from each block's `block.json`; **`block.json` wins on conflicts.**

import { getThemeBlockSettings, mergeThemeBlocksSettings } from '../helpers/blocks.js';

const blockSettings = {
	'core/button': {
		border: {
			color: false,
			radius: false,
			style: false,
			width: false,
		},
		color: {
			customDuotone: false,
			customGradient: false,
			defaultDuotone: false,
			defaultGradients: false,
			duotone: [],
			gradients: [],
		},
		typography: {
			fontSizes: [],
			fontFamilies: [],
			customFontSize: false,
			dropCap: false,
			fontStyle: false,
			fontWeight: false,
			textDecoration: false,
			textTransform: false,
		},
		spacing: {
			padding: true,
			margin: false,
		},
		shadow: {
			defaultPresets: false,
		},
	},
	'core/details': {
		spacing: {
			blockGap: false,
			margin: false,
			padding: false,
		},
		typography: {
			customFontSize: false,
			dropCap: false,
			fluid: false,
			fontFamilies: [],
			fontSizes: [],
			fontStyle: false,
			fontWeight: false,
			letterSpacing: false,
			lineHeight: false,
			textDecoration: false,
			textTransform: false,
		},
		border: {
			color: false,
			radius: false,
			style: false,
			width: false,
		},
	},
};

export default mergeThemeBlocksSettings(blockSettings, getThemeBlockSettings());
