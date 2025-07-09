import { defineConfig } from 'vite'
import path from 'path';
import liveReload from 'vite-plugin-live-reload';
import WPThemeJSONGenerator from './wp-theme-json-generator/src/index.js';

const THEME = '/wp-content/themes/wp-starter';

export default defineConfig(({ command }) => ({
	root: 'src',
	base: command === 'serve' ? '' : THEME + '/dist/',
	plugins: [
		{
			name: 'generate-theme-json',
			configureServer(server) {
				const generator = new WPThemeJSONGenerator({
					cssPath: 'src/styles/tailwind.css',
					outputPath: 'theme.json'
				});
				generator.watch();
			}
		},
		liveReload([
			path.resolve(__dirname, './blocks/**/*.twig'),
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

// Generate theme.json on build
const generator = new WPThemeJSONGenerator({
	cssPath: 'src/styles/tailwind.css',
	outputPath: 'theme.json'
});
generator.generateFile();
