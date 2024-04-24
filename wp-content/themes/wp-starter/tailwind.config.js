/** @type {import('tailwindcss').Config} */
import { remPair, rem } from "@viget/tailwindcss-plugins/utilities/fns/index.js";

// Breakpoints and content widths for the site
const minBreakpoint = 640;
const maxBreakpoint = 1440;

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

export default {
	darkMode: 'class',
	content: [
		'./src/**/*.{css,js,jsx,tsx,php}',
		'./patterns/**/*.{css,js,jsx,tsx,php}',
		'./parts/**/*.{css,js,jsx,tsx,php}',
		'./blocks/**/*.{css,js,jsx,tsx,php}',
	],
	theme: {
		contentSmall: minBreakpoint.toString(),
		contentBase: maxBreakpoint.toString(),
		extend: {
			colors: {
				transparent: 'transparent',
				//If you update the names or add more colors you will need to update the file in theme-json/settings/color.js
				white: "#ffffff",
				gray: {
					100: "#e5e5e5",
					500: "#737373",
					900: "#0a0a0a",
				},
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
				"lg": [["21px", fluidSize(21, 34)], "1.1"],
				"base": [["16px", fluidSize(16, 18)], "1.1"],
				"sm": [["12px", fluidSize(12, 16)], "1.1"],
				"xs": [["8px", fluidSize(8, 10)], "1.1"],
			},
			spacing: {
				// If you update the names or add more spacing you will need to update the file in theme-json/settings/spacing.js
				one: fluidSize(2, 16),
				two: fluidSize(20, 40),
				three: fluidSize(32, 64),
				four: fluidSize(56, 112),
				five: fluidSize(96, 160),
				six: fluidSize(144, 240),
				//These are not pulled into WordPress theme.json
				...remPair(0),
				...remPair(1),
				...remPair(2),
				...remPair(4),
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
		require('./plugins-tailwind/buttons.js'),
	],
}

