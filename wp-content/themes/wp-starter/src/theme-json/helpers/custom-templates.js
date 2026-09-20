import fs from 'fs';
import path from 'path';
import { toTitleCase } from './strings.js';

/**
 * Template files that authors pick from the page editor's Template control.
 *
 * WP only offers a template as a choice if theme.json lists it, and only a
 * chosen template writes `_wp_page_template` on the post — which is what
 * lets ACF field groups target it. A `page-{slug}.html` file matches by slug
 * on its own, but silently, so nothing records the choice.
 *
 * Discovered from `templates/page-*.html`, excluding `page.html` itself.
 */
function getCustomTemplates() {
	const dir = path.resolve(__dirname, '../../../templates');

	return fs
		.readdirSync(dir)
		.filter((file) => /^page-.+\.html$/.test(file))
		.map((file) => {
			const name = path.basename(file, '.html');

			return {
				name: name,
				title: toTitleCase(name.replace(/^page-/, '')),
				postTypes: ['page'],
			};
		});
}

export { getCustomTemplates };
