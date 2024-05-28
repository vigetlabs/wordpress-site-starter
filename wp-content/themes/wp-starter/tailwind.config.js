/** @type {import('tailwindcss').Config} */
const { remPair, rem } = require('@viget/tailwindcss-plugins/utilities/fns')

// Breakpoints and content widths for the site
const minBreakpoint = 640;
const maxBreakpoint = 1440;
/*
* This sets the accent color name which is used in theme-json/settings/color.js and plugins-tailwind/buttons.js
* Preferable use lowercase and one word. Use - for spaces and don't use capital letters or numbers.
* Any colors set in CSS will need to be manually updated.
*/
const accentColor = 'your-accent-color-name';

const fluidSize = (
	minSize,
	maxSize,
	unit = "vw",
) => {
	const slope = (maxSize - minSize) / (maxBreakpoint - minBreakpoint);
	const slopeToUnit = (slope * 100).toFixed(2);
	const interceptRem = (minSize - slope * minBreakpoint).toFixed(2);

	return `clamp(${rem(minSize)}, ${slopeToUnit}${unit} + ${rem(
		interceptRem,
	)}, ${rem(maxSize)})`;
};

module.exports = {
	content: [
		'./src/**/*.{css,js,jsx,tsx,php}',
		'./patterns/**/*.{css,js,jsx,tsx,php}',
		'./parts/**/*.{css,js,jsx,tsx,php,html}',
		'./blocks/**/*.{css,js,jsx,tsx,php}',
	],
	darkMode: 'selector',
	theme: {
		contentSmall: minBreakpoint.toString(),
		contentBase: maxBreakpoint.toString(),
		accentColor: accentColor,
		extend: {
			aspectRatio: {
				'5/3': '5/3',
				'5/4': '5/4',
			},
			colors: {
				transparent: 'transparent',
				//If you update the names or add more colors you will need to update the file in theme-json/settings/color.js
				white: "#ffffff",
				gray: {
					100: "#e5e5e5",
					500: "#737373",
					900: "#0a0a0a",
				},
				//This pulls in the accentColor as the color name
				[accentColor]: {
					50: "#f0f9ff",
					100: "#e0f2fe",
					200: "#bae6fd",
					300: "#7dd3fc",
					400: "#38bdf8",
					500: "#0ea5e9",
					600: "#0284c7",
					700: "#0369a1",
					800: "#075985",
					900: "#0c4a6e",
					950: "#082f49",
				},
			},
			flexBasis: {
				'1/2-gap': 'calc((100%/2) - var(--wp--style--block-gap))',
				'1/3-gap': 'calc((100%/3) - var(--wp--style--block-gap))',
				'1/4-gap': 'calc((100%/4) - var(--wp--style--block-gap))',
			},
			fontFamily: {
				// If you update the names or add more fonts you will need to update the file in theme-json/settings/typography.js
				// Fonts are handles WP's font loader.
				'sans': "Roboto Condensed, sans-serif",
			},
			fontSize: {
				// If you update the names or add more fonts sizes you will need to update the file in theme-json/settings/typography.js
				"2xl": [["32px", fluidSize(32, 60)],"1.1",],
				"xl": [["24px", fluidSize(24, 44)], "1.1"],
				"lg": [["20px", fluidSize(20, 34)], "1.1"],
				"base": [["16px", fluidSize(16, 18)], "1.1"],
				"sm": [["12px", fluidSize(12, 16)], "1.1"],
				"xs": [["8px", fluidSize(8, 10)], "1.1"],
			},
			spacing: {
				// If you update the names or add more spacing you will need to update the file in theme-json/settings/spacing.js
				"fluid-xs": fluidSize(2, 16),
				"fluid-sm": fluidSize(20, 40),
				"fluid-md": fluidSize(32, 64),
				"fluid-lg": fluidSize(56, 112),
				"fluid-xl": fluidSize(96, 160),
				"fluid-2xl": fluidSize(144, 240),
				//These are not pulled into WordPress theme.json
				...remPair(0),
				...remPair(1),
				...remPair(2),
				...remPair(4),
				...remPair(6),
				...remPair(8),
				...remPair(10),
				...remPair(12),
				...remPair(16),
				...remPair(20),
				...remPair(24),
				...remPair(28),
				...remPair(32),
				...remPair(36),
				...remPair(40),
				...remPair(44),
				...remPair(48),
				...remPair(52),
				...remPair(56),
				...remPair(60),
				...remPair(64),
				...remPair(80),
				...remPair(96),
				...remPair(112),
				...remPair(128),
			},
		},
	},
	plugins: [
		require('./plugins-tailwind/buttons.js')({
			accentColor: accentColor,
		}),
	],
}

