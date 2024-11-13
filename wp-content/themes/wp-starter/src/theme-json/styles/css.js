import color from '../settings/color.js';

let css = ':where(.wp-site-blocks *:focus){outline-width:2px;outline-style:solid}';

// loop through color.palette and add selectors for any colors that start with 'dark-'
for (const item of color.palette) {
	if (item.slug.startsWith('dark-')) {
		css += `.has-${item.slug}-background-color:not(.has-text-color),
		.has-${item.slug}-background-color:not(.has-text-color) .wp-block-button .wp-block-button__link:not(.has-text-color),
		.has-${item.slug}-background-color:not(.has-text-color) .wp-block-button.is-style-outline .wp-block-button__link:not(.has-text-color),
		.has-${item.slug}-background-color:not(.has-text-color) *:not(.has-text-color) {color:var(--wp--preset--color--white) !important;}`;
	}
}

export default css;
