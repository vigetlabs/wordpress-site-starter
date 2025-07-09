import * as fs from 'fs';
import * as path from 'path';

/**
 * Retrieve template parts from theme parts directory
 * and format them for use in theme.json
 * @param {string} partsPath - Path to the template parts directory
 * @returns {Array} Array of template part objects
 */
function getTemplateParts(partsPath = 'parts') {
	const fullPath = path.isAbsolute(partsPath) ? partsPath : path.join(process.cwd(), partsPath);
	
	try {
		const files = fs
			.readdirSync(fullPath)
			.filter((file) => /\.(php|html)$/.test(file))
			.filter(
				(file) =>
					!['header', 'footer'].includes(path.basename(file, path.extname(file))),
			);

		return files.map((file) => {
			const name = path.basename(file, path.extname(file));
			const title = name
				.replace(/[-_]/g, ' ')
				.replace(/\b\w/g, (char) => char.toUpperCase());

			return {
				name: name,
				title: title,
				area: 'uncategorized',
			};
		});
	} catch (error) {
		console.warn(`Template parts directory not found: ${fullPath}`);
		return [];
	}
}

export default function getTemplatePartsSettings(partsPath = 'parts') {
	return getTemplateParts(partsPath);
}
