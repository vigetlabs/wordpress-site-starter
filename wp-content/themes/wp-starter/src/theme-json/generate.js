import fs from 'fs';
import settings from './settings/_index.js';
import styles from './styles/_index.js';
import templateParts from './template-parts/_index.js';

const CONTENT_PATH = 'src/theme-json';

const generateThemeJSON = {
	name: 'generate-theme-json',
	configureServer(server) {
		// Watch the `CONTENT_PATH` directory for changes
		fs.watch(CONTENT_PATH, () => {
			// Regenerate the file when a change is detected
			buildJSON();
		});
	},
};

function buildJSON() {
	const data = {
		settings: settings,
		styles: styles,
		templateParts: templateParts,
		version: 3,
		$schema: 'https://schemas.wp.org/wp/6.8/theme.json',
	};

	fs.writeFileSync('theme.json', JSON.stringify(data, null, 2));
}

export default generateThemeJSON;
export { buildJSON };
