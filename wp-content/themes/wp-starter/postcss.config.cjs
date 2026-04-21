const postcssImportExtGlob = require('postcss-import-ext-glob');
const tailwindPostcss = require('@tailwindcss/postcss');
const cssnano = require('cssnano');
const { postcssPlugin: ffFluidFonts } = require('./src/plugins/fluid-font-calculations.cjs');

/**
 * Use an array here so postcss-load-config does not treat keys as npm package
 * names (e.g. `postcss-ff-fluid-fonts` would try require('postcss-ff-fluid-fonts')).
 */
module.exports = {
	plugins: [
		postcssImportExtGlob(),
		tailwindPostcss(),
		ffFluidFonts(),
		...(process.env.NODE_ENV === 'prod' ? [cssnano()] : []),
	],
};
