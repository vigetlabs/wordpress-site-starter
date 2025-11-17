import { getFontSizes } from '../helpers/fonts.js';

export default {
	fluid: true,
	letterSpacing: false,
	lineHeight: false,
	writingMode: false,
	defaultFontSizes: false,
	customFontSize: true,
	fontFamilies: [
		// For more info about loading fonts see https://fullsiteediting.com/lessons/theme-json-typography-options/
		{
			// Set this to be your body font family.
			fontFamily: 'Roboto Condensed',
			name: 'Roboto Condensed',
			slug: 'body',
			fontFace: [
				{
					fontFamily: 'Roboto Condensed',
					fontStretch: 'normal',
					fontStyle: 'normal',
					fontWeight: '300 900',
					// Fonts have to be woff2 to work with WP's font loader.
					src: ['file:./src/fonts/RobotoCondensed-VariableFont_wght.woff2'],
				},
				{
					fontFamily: 'Roboto Condensed Italic',
					fontStretch: 'normal',
					fontStyle: 'Italic',
					fontWeight: '300 900',
					// Fonts have to be woff2 to work with WP's font loader.
					src: [
						'file:./src/fonts/RobotoCondensed-Italic-VariableFont_wght.woff2',
					],
				},
			],
		},
		{
			// Set this to be your heading font family.
			fontFamily: 'Roboto Condensed',
			name: 'Roboto Condensed',
			slug: 'heading',
			/* Load custom fonts with fontFace
			fontFace: [
				{
					fontFamily: 'FONT_FAMILY_HERE',
					fontStretch: 'normal',
					fontStyle: 'normal',
					fontWeight: '300 900',
					src: ['file:./src/fonts/FONT_NAME_HERE.woff2'],
				},
			],*/
		},
		{
			fontFamily:
				'-apple-system, BlinkMacSystemFont, avenir next, avenir, segoe ui, helvetica neue, helvetica, Cantarell, Ubuntu, roboto, noto, arial, sans-serif',
			name: 'System Sans-serif',
			slug: 'system-sans-serif',
		},
		{
			fontFamily:
				'Iowan Old Style, Apple Garamond, Baskerville, Times New Roman, Droid Serif, Times, Source Serif Pro, serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol',
			name: 'System Serif',
			slug: 'system-serif',
		},
	],
	// Font sizes now use CSS custom properties defined in tailwind.css
	fontSizes: getFontSizes(),
};
