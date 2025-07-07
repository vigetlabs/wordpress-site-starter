import { defineConfig } from 'vite'
import path from 'path';
import liveReload from 'vite-plugin-live-reload';
import generateThemeJSON, { buildJSON } from './src/theme-json/generate_theme.js';

const THEME = '/wp-content/themes/wp-starter';

export default defineConfig(({ command }) => ({
	root: 'src',
	base: command === 'serve' ? '' : THEME + '/dist/',
	plugins: [
		generateThemeJSON,
		//Turn on if you want Vite to update with php files
		liveReload([
			path.resolve(__dirname, './templates/**/*'),
			path.resolve(__dirname, './parts/**/*'),
			path.resolve(__dirname, './blocks/**/*'),
		]),
	],
	build: {
		// output dir for production build
		outDir: '../dist',
		emptyOutDir: true,
		// emit manifest so PHP can find the hashed files
		manifest: true,
		// our entry
		rollupOptions: {
			input: {
				'main': path.resolve(__dirname, 'src/main.js'),
			}
		},
	},
	server: {
		host: "0.0.0.0",
		origin: "https://wpstarter.ddev.site:5273",
		strictPort: true,
		port: parseInt(process.env.VITE_PRIMARY_PORT ?? '5273'),
		watch: {
			usePolling: true,
			interval: 1000,
		},
		cors: true,
	},
}));

// Call buildJSON to generate the file on build
buildJSON()
