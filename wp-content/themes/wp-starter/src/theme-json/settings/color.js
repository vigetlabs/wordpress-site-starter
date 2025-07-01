import { theme as currentTheme } from '../../../tailwind.config.js';

/**
 * Converts a string to title case.
 * @param {string} str - The string to convert.
 * @returns {string} The title-cased string.
 */
function toTitleCase( str ) {
	str = str.replace(/[_-]/g, ' ');
	return str.replace(
		/\w\S*/g,
		text => text.charAt(0).toUpperCase() + text.substring(1).toLowerCase()
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
function isDark( color ) {
	// Convert hex to RGB
	const { r, g, b } = hexToRgb(color);

	// Calculate relative luminance
	const luminance = relativeLuminance(r, g, b);

	// Threshold for dark text is 0.179
	return luminance < 0.179;
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
	const colors = currentTheme.colors;

	for ( const color in colors ) {
		if ( ['transparent', 'current', 'currentColor'].includes( color ) ) {
			continue;
		}

		if ( typeof colors[color] === 'object' ) {
			for ( const shade in colors[color] ) {
				let slug = isDark( colors[color][shade] ) ? `dark-${color}-${shade}` : `${color}-${shade}`;
				let name = `${toTitleCase(color)} ${shade}`;
				palette.push( {
					color: colors[color][shade],
					name: name,
					slug: slug,
				} );
			}
		} else {
			let slug = isDark( colors[color] ) ? `dark-${color}` : color;
			let name = toTitleCase(color);
			palette.push( {
				color: colors[color],
				name: name,
				slug: slug,
			} );
		}
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

	if ( ! currentTheme.extend.backgroundImage ) {
		return gradients;
	}

	for ( const bgImage in currentTheme.extend.backgroundImage ) {
		if ( ! currentTheme.extend.backgroundImage[bgImage].toLowerCase().includes( 'gradient' ) ) {
			continue;
		}

		gradients.push( {
			name: bgImage.replace( 'gradient-', '' ).replace( '-', ' ' ).replace( /\b\w/g, char => char.toUpperCase() ),
			slug: bgImage.replace( 'gradient-', '' ),
			gradient: currentTheme.extend.backgroundImage[bgImage],
		} );
	}

	return gradients;
}

export default {
	defaultDuotone: false,
	defaultPalette: false,
	defaultGradients: false,

	palette: getPalette(),
	gradients: getGradients(),
};
