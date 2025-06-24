/** @type {import('tailwindcss').Config} */
const defaultTheme = require('tailwindcss/defaultTheme');
const { remPair, rem } = require('@viget/tailwindcss-plugins/utilities/fns')

// Breakpoints and content widths for the site
const minBreakpoint = 640; // Narrow site width, used with "wide" width alignment.
const maxBreakpoint = 1200; // Default site width.

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
		contentSmall: minBreakpoint.toString()+'px',
		contentBase: maxBreakpoint.toString()+'px',
		colors: {
			transparent: 'transparent',
			current: 'currentColor',
			white: "#ffffff",
			black: "#000000",
		},
		extend: {
			aspectRatio: {
				'5/3': '5/3',
				'5/4': '5/4',
			},
			flexBasis: {
				'1/2-gap': 'calc((100%/2) - var(--wp--style--block-gap))',
				'1/3-gap': 'calc((100%/3) - var(--wp--style--block-gap))',
				'1/4-gap': 'calc((100%/4) - var(--wp--style--block-gap))',
			},
			fontFamily: {
				'sans': 'var(--wp--preset--font-family--body)',
				'heading': 'var(--wp--preset--font-family--heading)',
			},
			fontSize: {
				// If you update the names or add more fonts sizes you will need to update the file in theme-json/settings/typography.js
				"2xl": [["32px", fluidSize(32, 60)], "1.1"],
				"xl": [["24px", fluidSize(24, 44)], "1.1"],
				"lg": [["20px", fluidSize(20, 34)], "1.1"],
				"base": [["16px", fluidSize(16, 18)], "1.1"],
				"sm": [["12px", fluidSize(12, 16)], "1.1"],
				"xs": [["8px", fluidSize(8, 10)], "1.1"],
				"zero": ["0px", "1.1"],
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
			screens: {
				"container": maxBreakpoint.toString()+'px',
				"wp-cols": "781px",
				"mobile-menu": "600px",
				"xs": "480px",
				...defaultTheme.screens,
			},
		},
	},
}

