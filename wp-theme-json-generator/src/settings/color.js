import fs from 'fs';
import path from 'path';

/**
 * Converts a string to title case.
 * @param {string} str - The string to convert.
 * @returns {string} The title-cased string.
 */
function toTitleCase(str) {
	str = str.replace(/[_-]/g, ' ');
	return str.replace(
		/\w\S*/g,
		(text) => text.charAt(0).toUpperCase() + text.substring(1).toLowerCase(),
	);
}

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

/**
 * Parse the CSS file to extract color variables from the @theme directive.
 *
 * @param {string} cssPath - Path to the CSS file
 * @returns {object} The color palette object
 */
function parseColorsFromCSS(cssPath = 'src/styles/tailwind.css') {
	const fullPath = path.isAbsolute(cssPath) ? cssPath : path.join(process.cwd(), cssPath);

	try {
		const cssContent = fs.readFileSync(fullPath, 'utf8');

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
 * Parse the CSS file to extract gradient variables from the @theme directive.
 *
 * @param {string} cssPath - Path to the CSS file
 * @returns {object} The gradient palette object
 */
function parseGradientsFromCSS(cssPath = 'src/styles/tailwind.css') {
	const fullPath = path.isAbsolute(cssPath) ? cssPath : path.join(process.cwd(), cssPath);

	try {
		const cssContent = fs.readFileSync(fullPath, 'utf8');

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
 * @param {string} cssPath - Path to the CSS file
 * @returns {*[]}
 */
function getPalette(cssPath = 'src/styles/tailwind.css') {
	const palette = [];
	const colors = parseColorsFromCSS(cssPath);

	for (const color in colors) {
		if (['transparent', 'current', 'currentColor'].includes(color)) {
			continue;
		}

		// For now, we're only handling simple color values
		// If you have color objects with shades, you'll need to extend this logic
		let slug = isDark(colors[color]) ? `dark-${color}` : color;
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
 * Get the gradients from the theme.
 *
 * @param {string} cssPath - Path to the CSS file
 * @returns {*[]}
 */
function getGradients(cssPath = 'src/styles/tailwind.css') {
	const gradients = [];
	const gradientVars = parseGradientsFromCSS(cssPath);

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

export default function getColorSettings(cssPath = 'src/styles/tailwind.css') {
	return {
		defaultDuotone: false,
		defaultPalette: false,
		defaultGradients: false,

		palette: getPalette(cssPath),
		gradients: getGradients(cssPath),
	};
}
