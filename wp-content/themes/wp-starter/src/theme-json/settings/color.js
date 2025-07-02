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
 * @returns {object} The color palette object
 */
function parseColorsFromCSS() {
	const cssPath = path.join(process.cwd(), 'src/styles/tailwind.css');

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
 * Get the color palette from the theme.
 *
 * Colors that are darker should be prefixed with 'dark-'
 * This allows us to use [class*="has-dark-"] in the css to change the HTML elements from a light to a dark version.
 *
 * @returns {*[]}
 */
function getPalette() {
	const palette = [];
	const colors = parseColorsFromCSS();

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
 * @returns {*[]}
 */
function getGradients() {
	const gradients = [];

	// Define gradients here if needed
	// Example:
	// const gradientDefinitions = {
	//   'gradient-to-r': 'linear-gradient(to right, var(--tw-gradient-stops))',
	//   'gradient-to-br': 'linear-gradient(to bottom right, var(--tw-gradient-stops))',
	// };

	// for ( const bgImage in gradientDefinitions ) {
	//   gradients.push( {
	//     name: bgImage.replace( 'gradient-', '' ).replace( '-', ' ' ).replace( /\b\w/g, char => char.toUpperCase() ),
	//     slug: bgImage.replace( 'gradient-', '' ),
	//     gradient: gradientDefinitions[bgImage],
	//   } );
	// }

	return gradients;
}

export default {
	defaultDuotone: false,
	defaultPalette: false,
	defaultGradients: false,

	palette: getPalette(),
	gradients: getGradients(),
};
