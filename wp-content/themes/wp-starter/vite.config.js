import { defineConfig } from 'vite'
import path from 'path';

const THEME = '/wp-content/themes/wp-starter';

export default defineConfig(({ command }) => ({
	root: 'src',
	base: command === 'serve' ? '' : '/dist/',
	plugins: [],
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
		hmr: {
			overlay: false,
		}
	},
}));
