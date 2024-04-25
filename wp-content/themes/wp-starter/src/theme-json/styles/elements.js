import theme from '../import_tailwind.js';

export default {
	caption: {
		color: {
			text: 'var(--wp--preset--color--black)',
		},
		typography: {
			fontFamily: 'var(--wp--preset--font-family--body)',
			fontSize: 'var(--wp--preset--font-size--x-small)',
		},
	},
	heading: {
		color: {
			text: 'var(--wp--preset--color--black)',
		},
		typography: {
			fontFamily: 'var(--wp--preset--font-family--heading)',
			fontWeight: '400',
			lineHeight: '1.2',
		},
	},
	link: {
		':hover': {
			typography: {
				textDecoration: 'none',
			},
		},
		color: {
			text: 'var(--wp--preset--color--black)',
		},
	},
};
