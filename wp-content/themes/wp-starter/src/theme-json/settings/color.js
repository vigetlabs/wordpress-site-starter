import theme from '../import_tailwind.js';

export default {
	defaultDuotone: false,
	defaultPalette: false,
	defaultGradients: false,
	/**
	 * Colors that are darker should be prefixed with 'dark-'
	 * This allows us to use [class*='has-dark'] in the css to change the HTML elements from a light to a dark version.
	 */
	palette: [
		{
			color: theme.colors.white,
			name: 'White',
			slug: 'white',
		},
		{
			color: theme.colors.gray[100],
			name: 'Gray 100',
			slug: 'gray-100',
		},
		{
			color: theme.colors.gray[500],
			name: 'Gray 500',
			slug: 'dark-gray-500',
		},
		{
			color: theme.colors.gray[900],
			name: 'Black',
			slug: 'dark-black',
		},
		{
			color: theme.colors[theme.accentColor][800],
			name: 'Accent',
			slug: 'dark-accent',
		},
	],
};
