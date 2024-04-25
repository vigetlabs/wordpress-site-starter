import theme from '../import_tailwind.js';

export default {
	spacingScale: {
		steps: 0,
	},
	spacingSizes: [
		{
			name: '1',
			size: theme.spacing['fluid-xs'],
			slug: '10',
		},
		{
			name: '2',
			size: theme.spacing['fluid-sm'],
			slug: '20',
		},
		{
			name: '3',
			size: theme.spacing['fluid-md'],
			slug: '30',
		},
		{
			name: '4',
			size: theme.spacing['fluid-lg'],
			slug: '40',
		},
		{
			name: '5',
			size: theme.spacing['fluid-xl'],
			slug: '50',
		},
		{
			name: '6',
			size: theme.spacing['fluid-2xl'],
			slug: '60',
		},
	],
	units: ['%', 'px', 'rem', 'vh', 'vw'],
};
