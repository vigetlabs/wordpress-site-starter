/**
 * This file is used to extract the font sizes from:
 * - src/styles/tailwind/typography.css
 */

import fs from 'fs';
import path from 'path';
import { createRequire } from 'node:module';
import { toTitleCase } from './strings.js';

const require = createRequire(import.meta.url);
const { getFluidTextDeclMap, tokenFromFluidVar, THEME_ROOT } = require('../../plugins/fluid-font-calculations.cjs');

/** Supported filters for block-specific font sizes (--text-{filter}-*). */
const SUPPORTED_FILTERS = ['headline', 'ui'];

const fontNames = {
	zero: 'Zero',
	tiny: 'Tiny',
	'2xs': '2X Small',
	xxs: '2X Small',
	xs: 'Extra Small',
	sm: 'Small',
	base: 'Base',
	md: 'Medium',
	lg: 'Large',
	xl: 'Extra Large',
	xxl: '2X Large',
	'2xl': '2X Large',
	xxxl: '3X Large',
	'3xl': '3X Large',
};

const fontSlugs = {
	zero: 'zero',
	tiny: 'tiny',
	'2xs': 'xx-small',
	xxs: 'xx-small',
	xs: 'x-small',
	sm: 'small',
	base: 'base',
	md: 'medium',
	lg: 'large',
	xl: 'x-large',
	xxl: '2x-large',
	'2xl': '2x-large',
	xxxl: '3x-large',
	'3xl': '3x-large',
};

/** Body-scale token names (no filter); derived from fontSlugs keys. */
const BODY_TOKEN_KEYS = Object.keys(fontSlugs);

/**
 * Get font sizes from the theme's typography CSS for theme.json.
 * Default (no filter): body scale (--text-* for any fontSlugs token), excluding zero.
 * With filter ("headline" or "ui"): only --text-{filter}-* tokens.
 * Returns size as the variable value from typography.css (e.g. var(--cfg-text-xs) or var(--fluid-text-m)).
 *
 * @param {string} filter - Optional filter: "headline" or "ui". Other values return [].
 * @returns {Array<{fluid: boolean, name: string, size: string, slug: string}>} The font sizes.
 */
function getFontSizes( filter = '' ) {
	if ( filter && !SUPPORTED_FILTERS.includes(filter) ) {
		return [];
	}

	const cssPath = path.join(THEME_ROOT, 'src/styles/tailwind/typography.css');
	const cssContent = fs.readFileSync(cssPath, 'utf8');
	const fluidClampByProp = getFluidTextDeclMap(THEME_ROOT);

	const matches = [];
	const regex = filter
		? new RegExp(`--text-${filter}-(\\w+):\\s*([^;]+);`, 'g')
		: new RegExp(`--text-(${BODY_TOKEN_KEYS.join('|')}):\\s*([^;]+);`, 'g');

	let match;
	while ( ( match = regex.exec( cssContent ) ) !== null ) {
		matches.push( { token: match[1], sizeValue: match[2].trim() } );
	}

	return matches
		.filter( ( { token } ) => ( filter ? true : token !== 'zero' ) )
		.map( ( { token, sizeValue } ) => {
			const fluidToken = tokenFromFluidVar(sizeValue);
			const size =
				fluidToken && fluidClampByProp.has(`--fluid-text-${fluidToken}`)
					? fluidClampByProp.get(`--fluid-text-${fluidToken}`)
					: sizeValue;

			return {
				fluid: false,
				name: fontNames[token] || toTitleCase( token ),
				size,
				slug: fontSlugs[token] || token.toLowerCase(),
			};
		} );
}

export { getFontSizes };
