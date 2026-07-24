/**
 * Canonical font-size vocabulary shared by helpers/fonts.js and the fluid-font-calculations
 * plugin (src/plugins/fluid-font-calculations.cjs), so both source from one place instead of
 * maintaining separate lists.
 *
 * Plain CommonJS (not ESM) so the plugin can `require()` it directly, and fonts.js (ESM) can
 * import its named exports via Node's standard CJS interop.
 */

/** Display name for each recognized font-size token. */
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

/** WP preset slug for each recognized font-size token. */
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

/**
 * Candidate suffixes for fluid-range discovery (--fs-{suffix}-min/max, optionally prefixed with
 * a SUPPORTED_FILTERS name below). Derived from fontSlugs' keys — there are more here than any
 * one scale actually defines; discovery skips whichever ones don't have a range in
 * font-sizes.css, so nothing needs to be hand-picked or kept in sync separately.
 */
const SIZE_SUFFIXES = Object.keys(fontSlugs);

/** Non-body font-size scales, addressed via a `--text-{filter}-*` prefix in typography.css. */
const SUPPORTED_FILTERS = ['headline', 'ui'];

module.exports = { fontNames, fontSlugs, SIZE_SUFFIXES, SUPPORTED_FILTERS };
