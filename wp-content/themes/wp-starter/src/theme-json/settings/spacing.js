import { getSpacingSizes } from '../helpers/spacing.js';

export default {
	spacingScale: {
		steps: 0,
	},
	defaultSpacingSizes: false,
	spacingSizes: getSpacingSizes(),
	units: ['%', 'px', 'rem', 'vh', 'vw'],
};
