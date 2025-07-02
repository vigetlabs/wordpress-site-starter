import theme from '../import_tailwind.js';

export default {
	spacingScale: {
		steps: 0,
	},
	defaultSpacingSizes: false,
	spacingSizes: [
		{
			name: '1',
			size: 'var(--spacing-fluid-xs)',
			slug: '10',
		},
		{
			name: '2',
			size: 'var(--spacing-fluid-sm)',
			slug: '20',
		},
		{
			name: '3',
			size: 'var(--spacing-fluid-md)',
			slug: '30',
		},
		{
			name: '4',
			size: 'var(--spacing-fluid-lg)',
			slug: '40',
		},
		{
			name: '5',
			size: 'var(--spacing-fluid-xl)',
			slug: '50',
		},
		{
			name: '6',
			size: 'var(--spacing-fluid-2xl)',
			slug: '60',
		},
	],
	units: ['%', 'px', 'rem', 'vh', 'vw'],
};
