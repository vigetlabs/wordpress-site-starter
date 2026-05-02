import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import settings from './settings/_index.js';
import styles from './styles/_index.js';
import templateParts from './template-parts/_index.js';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

/** Directory containing this file (`src/theme-json`). */
const THEME_JSON_DIR = __dirname;
/** Theme-root `blocks/` (not `src/blocks`). */
const THEME_BLOCKS_DIR = path.join(__dirname, '../../blocks');

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

		rebuild();

		const themeJsonAbs = path.normalize(THEME_JSON_DIR);
		const blocksAbs = path.normalize(THEME_BLOCKS_DIR);
		const fluidAbs = FLUID_CSS_WATCH_PATHS.map((p) => path.normalize(p));

		server.watcher.add(themeJsonAbs);
		if (fs.existsSync(THEME_BLOCKS_DIR)) {
			server.watcher.add(blocksAbs);
		}
		for (const watchPath of fluidAbs) {
			server.watcher.add(watchPath);
		}

		const shouldRebuild = (filePath) => {
			const normalized = path.normalize(filePath);
			if (normalized.startsWith(themeJsonAbs)) {
				return true;
			}
			if (
				normalized.endsWith('block.json') &&
				normalized.includes(`${path.sep}blocks${path.sep}`)
			) {
				return true;
			}
			if (fluidAbs.some((p) => normalized === p)) {
				return true;
			}
			return false;
		};

		server.watcher.on('change', (filePath) => {
			if (shouldRebuild(filePath)) {
				rebuild();
			}
		});
		server.watcher.on('add', (filePath) => {
			if (shouldRebuild(filePath)) {
				rebuild();
			}
		});
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
