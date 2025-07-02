import tailwindConfig from '../../tailwind.config.js';

function ensureObject(obj) {
	return typeof obj === 'object' && obj !== null ? obj : {};
}

const baseTheme = tailwindConfig.theme || {};
const extendedTheme = baseTheme.extend || {};

const mergedTheme = {
	...baseTheme,
	...extendedTheme,
	spacing: {
		...ensureObject(baseTheme.spacing),
		...ensureObject(extendedTheme.spacing),
	},
	fontSize: {
		md: '1rem', // Default value for md
		...ensureObject(baseTheme.fontSize),
		...ensureObject(extendedTheme.fontSize),
	},
	colors: {
		...ensureObject(baseTheme.colors),
		...ensureObject(extendedTheme.colors),
	},
	screens: {
		...ensureObject(baseTheme.screens),
		...ensureObject(extendedTheme.screens),
	},
};

// Always ensure the keys exist, even if empty
if (!mergedTheme.fontSize) mergedTheme.fontSize = { md: '1rem' };
if (!mergedTheme.spacing) mergedTheme.spacing = {};
if (!mergedTheme.colors) mergedTheme.colors = {};
if (!mergedTheme.screens) mergedTheme.screens = {};

export default mergedTheme;
