const fs = require('fs');
const path = require('path');

/**
 * Retrieve template parts from theme parts directory
 * and format them for use in theme.json
 */
function getTemplateParts() {
	const dir = path.resolve(__dirname, '../../../parts');
	const files = fs
		.readdirSync(dir)
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
}

const templateParts = getTemplateParts();

export default templateParts;
