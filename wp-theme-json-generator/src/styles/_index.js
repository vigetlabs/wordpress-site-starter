import blocks from './blocks.js';
import elements from './elements.js';
import getCSS from './css.js';

export default function getStyles(cssPath = 'src/styles/tailwind.css') {
	return {
		blocks: blocks,
		color: {
			background: 'var(--wp--preset--color--white)',
			text: 'var(--wp--preset--color--black)',
		},
		elements: elements,
		spacing: {
			blockGap: 0,
			padding: {
				left: 'var(--wp--preset--spacing--20)',
				right: 'var(--wp--preset--spacing--20)',
			},
		},
		typography: {
			fontFamily: 'var(--wp--preset--font-family--body)',
			fontSize: 'var(--wp--preset--font-size--medium)',
			fontStyle: 'normal',
			fontWeight: '400',
			lineHeight: '1.55',
		},
		css: getCSS(cssPath),
	};
}
