import { defineConfig } from 'vite'
import { resolve } from 'path';

const THEME = '/wp-content/themes/wp-starter';

export default defineConfig(({ command }) => ({
	root: 'src',
	base: command === 'serve' ? '' : THEME + '/dist/',
	plugins: [],
	build: {
		// generate manifest.json in outDir
		manifest: true,
		emptyOutDir: true,
		rollupOptions: {
			// overwrite default .html entry
			input: resolve(__dirname, 'src/main.js'),
		},
		outDir: '../dist',
	},
	server: {
		// respond to all network requests:
		host: "0.0.0.0",
		port: parseInt(process.env.VITE_PRIMARY_PORT ?? '5173'),
		strictPort: true,
		// Defines the origin of the generated asset URLs during development
		origin: "https://wpstarter.ddev.site:5173",
	},
}));
