import getColorSettings from '../settings/color.js';

export default function getCSS(cssPath = 'src/styles/tailwind.css') {
	const color = getColorSettings(cssPath);
	
	let css =
		':where(.wp-site-blocks *:focus-visible){outline-width:2px;outline-style:solid}';

	// loop through color.palette and add selectors for any colors that start with 'dark-'
	for (const item of color.palette) {
		if (item.slug.startsWith('dark-')) {
			css += `.has-${item.slug}-background-color:not(.has-text-color),
			.has-${item.slug}-background-color:not(.has-text-color) .wp-block-button .wp-block-button__link:not(.has-text-color),
			.has-${item.slug}-background-color:not(.has-text-color) .wp-block-button.is-style-outline .wp-block-button__link:not(.has-text-color),
			.has-${item.slug}-background-color:not(.has-text-color) *:not(.has-text-color,.has-inline-color):not(.wp-block-button__link-icon):not(svg, polygon) {color:var(--wp--preset--color--white) !important;}`;
		}
	}

	return css;
}
