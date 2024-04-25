import blocks from './blocks.js';
import elements from './elements.js';

const styles = {
	blocks: blocks,
	color: {
		background: 'var(--wp--preset--color--white)',
		text: 'var(--wp--preset--color--black)',
	},
	elements: elements,
	spacing: {
		blockGap: '1.2rem',
		padding: {
			left: 'var(--wp--preset--spacing--50)',
			right: 'var(--wp--preset--spacing--50)',
		},
	},
	typography: {
		fontFamily: 'var(--wp--preset--font-family--body)',
		fontSize: 'var(--wp--preset--font-size--medium)',
		fontStyle: 'normal',
		fontWeight: '400',
		lineHeight: '1.55',
	},
	css: ':where(.wp-site-blocks *:focus){outline-width:2px;outline-style:solid}',
};

export default styles;
