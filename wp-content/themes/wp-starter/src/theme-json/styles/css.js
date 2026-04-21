import color from '../settings/color.js';

let css =
	':where(.wp-site-blocks *:focus-visible){outline-width:2px;outline-style:solid}';

// Exclude elements inside light backgrounds (so they keep dark text) and inside map/embed containers
const lightBgExclusions = color.palette
	.filter((item) => !item.slug.startsWith('dark-'))
	.map(
		(item) =>
			`:not(.has-${item.slug}-background-color):not(.has-${item.slug}-background-color *)`,
	)
	.join('');
const mapAndLightBlockExclusions =
	':not(.sf-map-canvas):not(.sf-map-canvas *)' +
	':not(:has(div > .gm-style))' +
	':not(.acf-block-salesforce-map-embed):not(.acf-block-salesforce-map-embed *)';

// Loop through color.palette and add selectors for any colors that start with 'dark-'
for (const item of color.palette) {
	if (item.slug.startsWith('dark-')) {
		css += `.has-${item.slug}-background-color:not(.has-text-color),
		.has-${item.slug}-background-color:not(.has-text-color) .wp-block-button__link:not(.has-text-color),
		.has-${item.slug}-background-color:not(.has-text-color) .wp-block-button.is-style-outline .wp-block-button__link:not(.has-text-color),
		.has-${item.slug}-background-color:not(.has-text-color) *:not(.has-text-color,.has-inline-color):not(.wp-block-button__link-icon):not(svg, path, polygon):not(.components-button):not(.components-placeholder):not(.components-placeholder__label):not(.components-placeholder__instructions)${mapAndLightBlockExclusions}${lightBgExclusions}{color:var(--wp--preset--color--white);}`;
	}
}

export default css;
