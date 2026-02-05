/**
 * Vite plugin that restarts the dev server when CSS files are added or deleted.
 * This forces PostCSS to re-expand @import-glob patterns so changes are picked up
 * without requiring a manual DDEV restart.
 *
 * Uses server restart (not invalidation) because module invalidation doesn't reliably
 * trigger glob re-expansion - the new file isn't in the dependency graph yet.
 *
 * @param {object} options
 * @param {string[]} options.watchPaths - Array of glob patterns to watch
 */

import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

// Short debounce to coalesce rapid add/delete events
const DEBOUNCE_MS = 100;

const DEFAULT_WATCH_PATHS = [];

export default function viteGlobWatch(options = {}) {
	const { watchPaths = DEFAULT_WATCH_PATHS } = options;

	// Resolve paths in case relative paths were passed
	const resolvedPaths = watchPaths.map((p) =>
		path.isAbsolute(p) ? p : path.resolve(__dirname, p)
	);

	let server;
	let debounceTimer;

	return {
		name: 'vite-glob-watch',
		apply: 'serve',
		configureServer(_server) {
			server = _server;

			server.watcher.add(resolvedPaths);

			const handleAddOrUnlink = (file) => {
				if (!file.endsWith('.css')) return;

				if (debounceTimer) clearTimeout(debounceTimer);

				debounceTimer = setTimeout(() => {
					debounceTimer = null;
					server.restart();
				}, DEBOUNCE_MS);
			};

			server.watcher.on('add', handleAddOrUnlink);
			server.watcher.on('unlink', handleAddOrUnlink);
		},
	};
}
