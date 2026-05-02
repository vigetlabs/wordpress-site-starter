import fs from 'fs';
import path from 'path';
import { toTitleCase } from './strings.js';

/**
 * Converts a hexadecimal color to its RGB components.
 * @param {string} hex - The hexadecimal color (e.g., "#RRGGBB").
 * @returns {object} An object with the RGB components.
 */
function hexToRgb(hex) {
	// Remove the hash symbol if present
	hex = hex.replace(/^#/, '');

	// Parse the R, G, B values
	let bigint = parseInt(hex, 16);
	let r = (bigint >> 16) & 255;
	let g = (bigint >> 8) & 255;
	let b = bigint & 255;

	return { r, g, b };
}

/**
 * Calculates the relative luminance of a color.
 * @param {number} r - The red component (0-255).
 * @param {number} g - The green component (0-255).
 * @param {number} b - The blue component (0-255).
 * @returns {number} The relative luminance (0.0 - 1.0).
 */
function relativeLuminance(r, g, b) {
	// Convert RGB to sRGB
	const srgb = [r, g, b].map((v) => {
		v /= 255;
		return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
	});

	// Calculate the relative luminance
	return 0.2126 * srgb[0] + 0.7152 * srgb[1] + 0.0722 * srgb[2];
}

/**
 * Check if a color is dark.
 *
 * @param {string} color
 *
 * @returns {boolean}
 */
function isDark(color) {
	// Convert hex to RGB
	const { r, g, b } = hexToRgb(color);

	// Calculate relative luminance
	const luminance = relativeLuminance(r, g, b);

	// Threshold for dark text is 0.179
	return luminance < 0.179;
}

/** File whose @theme block defines colors synced to theme.json / the block editor. */
const EDITOR_PALETTE_CSS = path.join(
	process.cwd(),
	'src/styles/tailwind/inc/colors-palette.css',
);

/** Tailwind-only tokens; merged into `getPaletteAll()` / `getColorObject()` but excluded from global `getPalette()`. */
const EDITOR_UTILITIES_CSS = path.join(
	process.cwd(),
	'src/styles/tailwind/inc/colors-utilities.css',
);

/**
 * Parse the palette CSS file to extract color variables from the @theme directive.
 *
 * @returns {object} map of token name -> CSS value
 */
function parseColorsFromCSS() {
	const cssPath = EDITOR_PALETTE_CSS;

	try {
		const cssContent = fs.readFileSync(cssPath, 'utf8');

		// Find the @theme block
		const themeMatch = cssContent.match(/@theme\s*\{([\s\S]*?)\}/);
		if (!themeMatch) {
			console.warn('No @theme directive found in CSS file');
			return {};
		}

		const themeContent = themeMatch[1];

		// Extract color variables (--color-*)
		const colorRegex = /--color-([^:]+):\s*([^;]+);/g;
		const colors = {};
		let match;

		while ((match = colorRegex.exec(themeContent)) !== null) {
			const colorName = match[1].trim();
			const colorValue = match[2].trim();

			// Skip transparent and currentColor as they're not actual colors
			if (colorValue === 'transparent' || colorValue === 'currentColor') {
				continue;
			}

			colors[colorName] = colorValue;
		}

		return colors;
	} catch (error) {
		console.error('Error reading CSS file:', error);
		return {};
	}
}

/**
 * Parse colors-utilities.css @theme block (--color-* only).
 *
 * @returns {object} map of token name -> CSS value
 */
function parseUtilitiesColorsFromCSS() {
	try {
		if (!fs.existsSync(EDITOR_UTILITIES_CSS)) {
			return {};
		}

		const cssContent = fs.readFileSync(EDITOR_UTILITIES_CSS, 'utf8');
		const themeMatch = cssContent.match(/@theme\s*\{([\s\S]*?)\}/);
		if (!themeMatch) {
			return {};
		}

		const themeContent = themeMatch[1];
		const colorRegex = /--color-([^:]+):\s*([^;]+);/g;
		const colors = {};
		let match;

		while ((match = colorRegex.exec(themeContent)) !== null) {
			const colorName = match[1].trim();
			const colorValue = match[2].trim();

			if (colorValue === 'transparent' || colorValue === 'currentColor') {
				continue;
			}

			colors[colorName] = colorValue;
		}

		return colors;
	} catch (error) {
		console.error('Error reading utilities CSS file:', error);
		return {};
	}
}

/**
 * Parse the palette CSS file to extract gradient variables from the @theme directive.
 *
 * @returns {object} The gradient palette object
 */
function parseGradientsFromCSS() {
	const cssPath = EDITOR_PALETTE_CSS;

	try {
		const cssContent = fs.readFileSync(cssPath, 'utf8');

		// Find the @theme block
		const themeMatch = cssContent.match(/@theme\s*\{([\s\S]*?)\}/);
		if (!themeMatch) {
			console.warn('No @theme directive found in CSS file');
			return {};
		}

		const themeContent = themeMatch[1];

		// Extract gradient variables (--gradient-*)
		const gradientRegex = /--gradient-([^:]+):\s*([^;]+);/g;
		const gradients = {};
		let match;

		while ((match = gradientRegex.exec(themeContent)) !== null) {
			const gradientName = match[1].trim();
			const gradientValue = match[2].trim();

			gradients[gradientName] = gradientValue;
		}

		return gradients;
	} catch (error) {
		console.error('Error reading CSS file:', error);
		return {};
	}
}

/**
 * Get the color palette from the theme.
 *
 * Colors that are darker should be prefixed with 'dark-'
 * This allows us to use [class*="has-dark-"] in the css to change the HTML elements from a light to a dark version.
 *
 * Special color names (base, contrast, accent-*) keep their original slug regardless of darkness.
 *
 * @returns {*[]}
 */
function getPalette() {
	const palette = [];
	const colors = parseColorsFromCSS();

	// Colors that should keep their original slug (no dark- prefix)
	const specialColors = ['base', 'contrast', 'accent-4', 'accent-5', 'accent-6'];

	for (const color in colors) {
		if (['transparent', 'current', 'currentColor'].includes(color)) {
			continue;
		}

		// For now, we're only handling simple color values
		// If you have color objects with shades, you'll need to extend this logic
		// Special colors keep their original slug, others get dark- prefix if dark
		let slug;
		if (specialColors.includes(color)) {
			slug = color;
		} else {
			slug = isDark(colors[color]) ? `dark-${color}` : color;
		}
		let name = toTitleCase(color);
		palette.push({
			color: colors[color],
			name: name,
			slug: slug,
		});
	}

	return palette;
}

/**
 * Utility-only palette entries (same slug rules as {@link getPalette}).
 *
 * @returns {Array<{ color: string, name: string, slug: string }>}
 */
function getUtilitiesPalette() {
	const palette = [];
	const colors = parseUtilitiesColorsFromCSS();

	const specialColors = ['base', 'contrast', 'accent-4', 'accent-5', 'accent-6', 'accent'];

	for (const color in colors) {
		if (['transparent', 'current', 'currentColor'].includes(color)) {
			continue;
		}

		let slug;
		if (specialColors.includes(color)) {
			slug = color;
		} else {
			slug = isDark(colors[color]) ? `dark-${color}` : color;
		}

		palette.push({
			color: colors[color],
			name: toTitleCase(color),
			slug,
		});
	}

	return palette;
}

/**
 * Palette tokens from colors-palette.css plus colors-utilities.css (for slug resolution in block.json).
 * Core entries win when slug matches.
 *
 * @returns {Array<{ color: string, name: string, slug: string }>}
 */
function getPaletteAll() {
	const core = getPalette();
	const utils = getUtilitiesPalette();
	const bySlug = new Map();

	for (const entry of core) {
		bySlug.set(entry.slug, entry);
	}

	for (const entry of utils) {
		if (!bySlug.has(entry.slug)) {
			bySlug.set(entry.slug, entry);
		}
	}

	return [...bySlug.values()];
}

/**
 * Gradients from colors-palette.css @theme plus optional utilities file.
 *
 * @returns {Array<{ gradient: string, name: string, slug: string }>}
 */
function parseGradientsFromUtilitiesCSS() {
	try {
		if (!fs.existsSync(EDITOR_UTILITIES_CSS)) {
			return {};
		}

		const cssContent = fs.readFileSync(EDITOR_UTILITIES_CSS, 'utf8');
		const themeMatch = cssContent.match(/@theme\s*\{([\s\S]*?)\}/);
		if (!themeMatch) {
			return {};
		}

		const themeContent = themeMatch[1];
		const gradientRegex = /--gradient-([^:]+):\s*([^;]+);/g;
		const gradients = {};
		let match;

		while ((match = gradientRegex.exec(themeContent)) !== null) {
			gradients[match[1].trim()] = match[2].trim();
		}

		return gradients;
	} catch (error) {
		console.error('Error reading utilities gradients:', error);
		return {};
	}
}

/**
 * Get the gradients from the theme.
 *
 * @returns {*[]}
 */
function getGradients() {
	const gradients = [];
	const gradientVars = parseGradientsFromCSS();

	for (const gradient in gradientVars) {
		let slug = gradient;
		let name = toTitleCase(gradient);
		gradients.push({
			gradient: gradientVars[gradient],
			name: name,
			slug: slug,
		});
	}

	return gradients;
}

/**
 * Gradients from palette + utilities (utilities fill slugs not in core).
 *
 * @returns {Array<{ gradient: string, name: string, slug: string }>}
 */
function getGradientsAll() {
	const core = getGradients();
	const gradientVars = parseGradientsFromUtilitiesCSS();
	const extras = [];

	for (const key in gradientVars) {
		extras.push({
			gradient: gradientVars[key],
			name: toTitleCase(key),
			slug: key,
		});
	}

	const bySlug = new Map();
	for (const entry of core) {
		bySlug.set(entry.slug, entry);
	}
	for (const entry of extras) {
		if (!bySlug.has(entry.slug)) {
			bySlug.set(entry.slug, entry);
		}
	}

	return [...bySlug.values()];
}

/**
 * Global duotone presets (extend when duotone tokens are added to theme CSS or theme.json).
 *
 * @returns {Array<{ colors: string[], name: string, slug: string }>}
 */
function getDuotones() {
	return [];
}

/**
 * Get a color object from the palette or utilities.
 *
 * @param {string} color - The color slug.
 *
 * @returns {object|undefined} The color object.
 */
function getColorObject(color) {
	return getPaletteAll().find((item) => item.slug === color);
}

/**
 * Resolve a gradient preset by slug.
 *
 * @param {string} slug
 * @returns {object|undefined}
 */
function getGradientObject(slug) {
	return getGradientsAll().find((item) => item.slug === slug);
}

/**
 * Resolve a duotone preset by slug.
 *
 * @param {string} slug
 * @returns {object|undefined}
 */
function getDuotoneObject(slug) {
	return getDuotones().find((item) => item.slug === slug);
}

export {
	getPalette,
	getPaletteAll,
	getGradients,
	getGradientsAll,
	getDuotones,
	getColorObject,
	getGradientObject,
	getDuotoneObject,
};
