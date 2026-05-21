import { getPalette, getGradients } from '../helpers/colors.js';

/**
 * Global preset lists come from `colors-palette.css`. Tokens in `colors-utilities.css` stay out of the global palette.
 */
export default {
	defaultDuotone: false,
	defaultPalette: false,
	defaultGradients: false,

	palette: getPalette(),
	gradients: getGradients(),
};
