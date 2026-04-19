import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import settings from './settings/_index.js';
import styles from './styles/_index.js';
import templateParts from './template-parts/_index.js';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

/** Directory containing this file (`src/theme-json`). */
const THEME_JSON_DIR = __dirname;

/** Fluid typography sources: changing breakpoints or font ranges should refresh theme.json presets. */
const FLUID_CSS_WATCH_PATHS = [
	path.join(__dirname, '../styles/tailwind/tailwind.css'),
	path.join(__dirname, '../styles/tailwind/inc/font-sizes.css'),
];

const generateThemeJSON = {
	name: 'generate-theme-json',
	configureServer(server) {
		const rebuild = () => {
			buildJSON();
		};

		fs.watch(THEME_JSON_DIR, rebuild);

		for (const watchPath of FLUID_CSS_WATCH_PATHS) {
			fs.watch(watchPath, rebuild);
		}
	},
};

function buildJSON() {
	const data = {
		settings: settings,
		styles: styles,
		templateParts: templateParts,
		version: 3,
		$schema: 'https://schemas.wp.org/wp/6.9/theme.json',
	};

	fs.writeFileSync('theme.json', JSON.stringify(data, null, 2));
}

export default generateThemeJSON;
export { buildJSON };
