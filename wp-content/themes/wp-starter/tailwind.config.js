/** @type {import('tailwindcss').Config} */
//const { remPair, rem, em } = require('@viget/tailwindcss-plugins/utilities/fns')

export default {
  content: [
		'./src/**/*.{css,js,jsx,tsx,php}',
		'./**/**/*.{php,css,js}',
	],
  theme: {
		extend: {
			colors: {
				/*
				 * These Colors get automatically added to the theme.json
				 * If you update the names or add more colors you will need to update the file in theme-json/settings/color.js
				*/
				base: {
					100: "#ffffff",
					200: "#e5e5e5", 
				},
				contrast: {
					100: "#0a0a0a",
					200: "#737373",
					300: "#a3a3a3",
				},
				accent: {
					100: "#cfcabe",
					200: "#c2a990",
					300: "#0ea5e9",
					400: "#0369a1",
					500: "#0c4a6e",
				}
			}
			
		},
  },
  plugins: [],
}

