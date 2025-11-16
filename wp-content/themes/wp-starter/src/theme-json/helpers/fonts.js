import fs from 'fs';
import path from 'path';
import { toTitleCase } from './strings.js';

/**
 * Get the font sizes from the styles/tailwind/typography.css file.
 * @returns {Object} The font sizes.
 */
function getFontSizes() {
	const cssPath = path.join(process.cwd(), 'src/styles/tailwind/typography.css');
	const cssContent = fs.readFileSync(cssPath, 'utf8');
	const fontSizes = cssContent.match(/--text-\w+:\s*([^;]+);/g);

	const fontNames = {
		tiny: 'Tiny',
		'2xs': '2X Small',
		xxs: '2X Small',
		xs: 'Extra Small',
		sm: 'Small',
		md: 'Medium',
		lg: 'Large',
		xl: 'Extra Large',
		xxl: '2X Large',
		'2xl': '2X Large',
	};

	const fontSlugs = {
		tiny: 'tiny',
		'2xs': 'xx-small',
		xxs: 'xx-small',
		xs: 'x-small',
		sm: 'small',
		md: 'medium',
		lg: 'large',
		xl: 'x-large',
		xxl: '2x-large',
		'2xl': '2x-large',
	};

	return fontSizes.flatMap((fontSize) => {
		const match = fontSize.match(/--text-(\w+):\s*([^;]+);/);
		const [, size, value] = match;

		if (size === 'zero') return [];

		return [{
			fluid: false,
			name: fontNames[size] || toTitleCase(size),
			size: value.trim(),
			slug: fontSlugs[size] || size.toLowerCase(),
		}];
	});
}

export { getFontSizes };
