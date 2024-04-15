import { defineConfig } from 'vite'
import path from 'path'



export default {
	plugins: [],
	build: {
		// generate manifest.json in outDir
		manifest: true,
		rollupOptions: {
				// overwrite default .html entry
				input: 'src/main.js',
		},
		outDir: 'dist',
	},
	server: {
		// respond to all network requests:
		host: "0.0.0.0",
		port: parseInt(process.env.VITE_PRIMARY_PORT ?? '5173'),
		strictPort: true,
		// Defines the origin of the generated asset URLs during development
		origin: "https://wpstarter.ddev.site:5173",
	},
};
