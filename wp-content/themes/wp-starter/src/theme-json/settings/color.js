import { getPalette, getGradients } from '../helpers/colors.js';

/**
 * Palette and gradients come from `src/styles/tailwind/inc/colors-palette.css` only.
 * Add Tailwind-only tokens in `colors-utilities.css` so they stay out of the block editor.
 */
export default {
	defaultDuotone: false,
	defaultPalette: false,
	defaultGradients: false,

	palette: getPalette(),
	gradients: getGradients(),
};
