import * as fs from 'fs';
import settings from './settings/_index.js';
import styles from './styles/_index.js';

const CONTENT_PATH = 'src/theme-json';

export default {
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
		$schema: 'https://schemas.wp.org/trunk/theme.json',
		version: 2,
		settings: settings,
		styles: styles,
	};

	fs.writeFileSync('theme.json', JSON.stringify(data));
}
export { buildJSON };
