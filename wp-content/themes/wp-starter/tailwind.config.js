/** @type {import('tailwindcss').Config} */
export default {
  content: [
		'./src/**/*.{css,js,jsx,tsx,php}',
		'./**/**/*.{php,css,js}',
	],
  theme: {
		extend: {
			colors: {
				//Colors are set in the WordPress theme.json
				base: '#000000',
				contrast: '#232323',
			}
		},
  },
  plugins: [],
}

