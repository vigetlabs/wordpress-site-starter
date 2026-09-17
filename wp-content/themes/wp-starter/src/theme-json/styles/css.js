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
	':not(.acf-block-salesforce-map-embed):not(.acf-block-salesforce-map-embed *)' +
	// Core Search renders its own field colors; repainting them washes the input out.
	':not(.wp-block-search__input):not(.wp-block-search__inside-wrapper)';

/**
 * Any palette color class, whether or not WP paired it with `has-text-color`.
 * Some blocks (Social Icons' `iconColor`) emit `has-{slug}-color` on its own, so
 * `:not(.has-text-color)` never sees the editor's choice. Built from the palette
 * so new tokens are covered without touching this file.
 */
const explicitColorExclusions = `:not(:is(${color.palette
	.map((item) => `.has-${item.slug}-color`)
	.join(',')}))`;

/**
 * Blocks that color themselves through a non-text attribute, where the chosen
 * color has to reach children the auto-contrast rules would otherwise repaint.
 * Add a block-level selector here when its color control writes something other
 * than `textColor`.
 *
 * - `core/social-links`: `iconColor` sets `color` on each `li`; the anchor and
 *   its SVG inherit it through core's `currentColor` rules.
 */
const selfColoredBlocks = ['.wp-block-social-links.has-icon-color'].join(',');

/** Element-level color choices plus the self-coloring blocks and their children. */
const editorColorExclusions =
	explicitColorExclusions +
	`:not(:is(${selfColoredBlocks})):not(:is(${selfColoredBlocks}) *)`;

// Loop through color.palette and add selectors for any colors that start with 'dark-'
for (const item of color.palette) {
	if (item.slug.startsWith('dark-')) {
		css += `.has-${item.slug}-background-color:not(.has-text-color),
		.has-${item.slug}-background-color:not(.has-text-color) .wp-block-button__link:not(.has-text-color),
		.has-${item.slug}-background-color:not(.has-text-color) .wp-block-button.is-style-outline .wp-block-button__link:not(.has-text-color),
		.has-${item.slug}-background-color:not(.has-text-color) *:not(.has-text-color,.has-inline-color):not(.wp-block-button__link-icon):not(svg, path, polygon):not(.components-button):not(.components-placeholder):not(.components-placeholder__label):not(.components-placeholder__instructions)${editorColorExclusions}${mapAndLightBlockExclusions}${lightBgExclusions}{color:var(--wp--preset--color--white);}`;
	}
}

export default css;
