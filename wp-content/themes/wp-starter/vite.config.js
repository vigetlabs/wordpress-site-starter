import { defineConfig } from 'vite'
import path from 'path';
import liveReload from 'vite-plugin-live-reload';
import { ViteImageOptimizer } from 'vite-plugin-image-optimizer';
import generateThemeJSON, { buildJSON } from './src/theme-json/generate.js';
import viteGlobWatch from './src/plugins/vite-glob-watch.js';
import viteEditorStyles from './src/plugins/vite-scoped-editor-styles.js';
import rewriteImagesAlias from './src/plugins/rewrite-image-alias-urls.js';

const dirname = import.meta.dirname;
const THEME = '/wp-content/themes/wp-starter';
const VITE_PORT = parseInt(process.env.VITE_PRIMARY_PORT ?? '5273');

export default defineConfig(({ command }) => ({
	root: 'src',
	base: command === 'serve' ? '' : THEME + '/dist/',
	// See README.md > Images.
	publicDir: path.resolve(dirname, 'src/public'),
	resolve: {
		alias: {
			'@images': path.resolve(dirname, 'src/public/images'),
		},
	},
	plugins: [
		generateThemeJSON,
		liveReload([
			path.resolve(dirname, './blocks/**/*.twig'),
			path.resolve(dirname, './theme.json'),
			path.resolve(dirname, './**/*.php'),
		]),
		viteGlobWatch({
			watchPaths: [
				path.resolve(dirname, './src/**/*.css'),
				path.resolve(dirname, './blocks/**/*.css'),
			],
		}),
		viteEditorStyles({
			cssEntry: path.resolve(dirname, 'src/styles/editor.css'),
			watchPaths: [
				path.resolve(dirname, './src/**/*.css'),
				path.resolve(dirname, './blocks/**/*.css'),
			],
		}),
		rewriteImagesAlias(),
		ViteImageOptimizer(),
	],
	build: {
		// output dir for production build
		outDir: '../dist',
		emptyOutDir: true,
		// emit manifest so PHP can find the hashed files
		manifest: true,
		minify: 'terser',
		rollupOptions: {
			input: {
				main:   path.resolve(dirname, 'src/main.js'),
				admin:  path.resolve(dirname, 'src/admin.js'),
				editor: path.resolve(dirname, 'src/editor.js'),
			},
		},
	},
	server: {
		host: "0.0.0.0",
		origin: `https://wpstarter.ddev.site:${VITE_PORT}`,
		strictPort: true,
		port: VITE_PORT,
		watch: {
			usePolling: true,
			interval: 1000,
		},
		cors: true,
	},
}));

// Call buildJSON to generate the file on build
buildJSON();
