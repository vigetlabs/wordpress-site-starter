import { defineConfig } from 'vite'
import path from 'path';
import liveReload from 'vite-plugin-live-reload';
import generateThemeJSON, { buildJSON } from './src/theme-json/generate_theme.js';

export default defineConfig(({ command }) => ({
	root: 'src',
	base: command === 'serve' ? '' : '/dist/',
	plugins: [
		generateThemeJSON,
		liveReload([
			path.resolve(__dirname, './src/**/*'),
			path.resolve(__dirname, './templates/**/*'),
			path.resolve(__dirname, './parts/**/*'),
			path.resolve(__dirname, './blocks/**/*'),
			path.resolve(__dirname, './**/*.php'),
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
				'editor': path.resolve(__dirname, 'src/editor.js'),
			}
		},
	},
	server: {
		host: "0.0.0.0",
		origin: "https://wpstarter.ddev.site:5273",
		strictPort: true,
		port: parseInt(process.env.VITE_PRIMARY_PORT ?? '5273'),
	},
}));

// Call buildJSON to generate the file on build
buildJSON()
