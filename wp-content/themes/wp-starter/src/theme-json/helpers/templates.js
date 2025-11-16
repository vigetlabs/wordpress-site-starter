import fs from 'fs';
import path from 'path';
import { toTitleCase } from './strings.js';

/**
 * Retrieve template parts from theme parts directory
 * and format them for use in theme.json
 */
function getTemplateParts() {
	const dir = path.resolve(__dirname, '../../../parts');
	const files = fs
		.readdirSync(dir)
		.filter((file) => /\.(php|html)$/.test(file));

	return files.map((file) => {
		const name = path.basename(file, path.extname(file));
		const title = toTitleCase(name);

		let area = 'uncategorized';
		if (file.includes('header')) area = 'header';
		if (file.includes('footer')) area = 'footer';

		return {
			name: name,
			title: title,
			area: area,
		};
	});
}

export { getTemplateParts };
