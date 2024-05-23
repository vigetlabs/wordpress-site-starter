//Move any block that you are working to a new file to make it easyer to maintain
import theme from '../import_tailwind.js';
import image from './blocks/image.js';

const blocks = {
	'core/avatar': {
		border: {
			radius: '90px',
		},
	},
	'core/buttons': {
		spacing: {
			blockGap: theme.spacing[12],
		},
	},
	'core/categories': {
		spacing: {
			padding: {
				left: '0px',
				right: '0px',
			},
		},
		css: '& {list-style-type:none;} & li{margin-bottom: 0.5rem;}',
	},
	'core/code': {
		color: {
			background: 'var(--wp--preset--color--white)',
			text: 'var(--wp--preset--color--gray-500)',
		},
		spacing: {
			padding: {
				bottom: 'var(--wp--preset--spacing--20)',
				left: 'var(--wp--preset--spacing--20)',
				right: 'var(--wp--preset--spacing--20)',
				top: 'var(--wp--preset--spacing--20)',
			},
		},
		typography: {
			fontSize: 'var(--wp--preset--font-size--medium)',
			fontStyle: 'normal',
			fontWeight: '400',
			lineHeight: '1.6',
		},
	},
	'core/comment-author-name': {
		color: {
			text: 'var(--wp--preset--color--black)',
		},
		elements: {
			link: {
				color: {
					text: 'var(--wp--preset--color--black)',
				},
				typography: {
					textDecoration: 'none',
				},
				':hover': {
					typography: {
						textDecoration: 'underline',
					},
				},
			},
		},
		typography: {
			fontSize: 'var(--wp--preset--font-size--small)',
			fontStyle: 'normal',
			fontWeight: '600',
		},
	},
	'core/comment-content': {
		typography: {
			fontSize: 'var(--wp--preset--font-size--small)',
		},
		spacing: {
			margin: {
				top: 'var(--wp--preset--spacing--20)',
				bottom: 'var(--wp--preset--spacing--20)',
			},
		},
	},
	'core/comment-date': {
		color: {
			text: 'var(--wp--preset--color--black)',
		},
		elements: {
			link: {
				color: {
					text: 'var(--wp--preset--color--black)',
				},
				typography: {
					textDecoration: 'none',
				},
				':hover': {
					typography: {
						textDecoration: 'underline',
					},
				},
			},
		},
		typography: {
			fontSize: 'var(--wp--preset--font-size--small)',
		},
		spacing: {
			margin: {
				top: '0px',
				bottom: '0px',
			},
		},
	},
	'core/comment-edit-link': {
		elements: {
			link: {
				color: {
					text: 'var(--wp--preset--color--black)',
				},
				typography: {
					textDecoration: 'none',
				},
				':hover': {
					typography: {
						textDecoration: 'underline',
					},
				},
			},
		},
		typography: {
			fontSize: 'var(--wp--preset--font-size--small)',
		},
	},
	'core/comment-reply-link': {
		elements: {
			link: {
				color: {
					text: 'var(--wp--preset--color--black)',
				},
				typography: {
					textDecoration: 'none',
				},
				':hover': {
					typography: {
						textDecoration: 'underline',
					},
				},
			},
		},
		typography: {
			fontSize: 'var(--wp--preset--font-size--small)',
		},
	},
	'core/post-comments-form': {
		css: '& textarea, input{border-radius:.33rem}',
	},
	'core/comments-pagination': {
		typography: {
			fontSize: 'var(--wp--preset--font-size--small)',
		},
	},
	'core/comments-pagination-next': {
		typography: {
			fontSize: 'var(--wp--preset--font-size--small)',
		},
	},
	'core/comments-pagination-numbers': {
		typography: {
			fontSize: 'var(--wp--preset--font-size--small)',
		},
	},
	'core/comments-pagination-previous': {
		typography: {
			fontSize: 'var(--wp--preset--font-size--small)',
		},
	},
	'core/footnotes': {
		typography: {
			fontSize: 'var(--wp--preset--font-size--small)',
		},
	},
	'core/gallery': {
		spacing: {
			margin: {
				bottom: 'var(--wp--preset--spacing--50)',
			},
		},
	},
	...image,
	'core/list': {
		spacing: {
			padding: {
				left: 'var(--wp--preset--spacing--10)',
			},
		},
	},
	'core/loginout': {
		css: '& input{border-radius:.33rem;padding:calc(0.667em + 2px);border:1px solid #949494;}',
	},
	'core/post-author': {
		typography: {
			fontSize: 'var(--wp--preset--font-size--small)',
		},
	},
	'core/post-author-name': {
		elements: {
			link: {
				':hover': {
					typography: {
						textDecoration: 'underline',
					},
				},
				typography: {
					textDecoration: 'none',
				},
			},
		},
		typography: {
			fontSize: 'var(--wp--preset--font-size--small)',
		},
	},
	'core/post-date': {
		color: {
			text: 'var(--wp--preset--color--black)',
		},
		elements: {
			link: {
				':hover': {
					typography: {
						textDecoration: 'underline',
					},
				},
				color: {
					text: 'var(--wp--preset--color--black)',
				},
				typography: {
					textDecoration: 'none',
				},
			},
		},
		typography: {
			fontSize: 'var(--wp--preset--font-size--small)',
		},
	},
	'core/post-excerpt': {
		typography: {
			lineHeight: '1.6',
		},
	},
	'core/post-featured-image': {
		border: {
			radius: 'var(--wp--preset--spacing--20)',
		},
	},
	'core/post-terms': {
		elements: {
			link: {
				':hover': {
					typography: {
						textDecoration: 'underline',
					},
				},
				typography: {
					textDecoration: 'none',
				},
			},
		},
		typography: {
			fontSize: 'var(--wp--preset--font-size--small)',
		},
		css: '& .wp-block-post-terms__prefix{color: var(--wp--preset--color--black);}',
	},
	'core/post-title': {
		elements: {
			link: {
				':hover': {
					typography: {
						textDecoration: 'underline',
					},
				},
				typography: {
					textDecoration: 'none',
				},
			},
		},
	},
	'core/pullquote': {
		elements: {
			cite: {
				typography: {
					fontFamily: 'var(--wp--preset--font-family--body)',
					fontSize: 'var(--wp--preset--font-size--medium)',
					fontStyle: 'italic',
				},
			},
		},
		spacing: {
			padding: {
				bottom: 'var(--wp--preset--spacing--40)',
				top: 'var(--wp--preset--spacing--40)',
			},
		},
		typography: {
			fontFamily: 'var(--wp--preset--font-family--heading)',
			fontSize: 'var(--wp--preset--font-size--x-large)',
			lineHeight: "1"
		},
	},
	'core/query-title': {
		css: '& span {font-style: italic;}',
	},
	'core/query-no-results': {
		spacing: {
			padding: {
				top: 'var(--wp--preset--spacing--30)',
			},
		},
	},
	'core/quote': {
		css: '& :where(p) {margin-block-start:0;margin-block-end:calc(var(--wp--preset--spacing--10) + 0.5rem);} & :where(:last-child) {margin-block-end:0;} &.has-text-align-right.is-style-plain, .rtl .is-style-plain.wp-block-quote:not(.has-text-align-center):not(.has-text-align-left){border-width: 0 2px 0 0;padding-left:calc(var(--wp--preset--spacing--20) + 0.5rem);padding-right:calc(var(--wp--preset--spacing--20) + 0.5rem);} &.has-text-align-left.is-style-plain, body:not(.rtl) .is-style-plain.wp-block-quote:not(.has-text-align-center):not(.has-text-align-right){border-width: 0 0 0 2px;padding-left:calc(var(--wp--preset--spacing--20) + 0.5rem);padding-right:calc(var(--wp--preset--spacing--20) + 0.5rem)}',
		elements: {
			cite: {
				typography: {
					fontFamily: 'var(--wp--preset--font-family--body)',
					fontSize: 'var(--wp--preset--font-size--small)',
					fontStyle: 'normal',
				},
			},
		},
		spacing: {
			padding: {
				bottom: 'calc(var(--wp--preset--spacing--20) + 0.75rem)',
				left: 'calc(var(--wp--preset--spacing--20) + 0.75rem)',
				right: 'calc(var(--wp--preset--spacing--20) + 0.75rem)',
				top: 'calc(var(--wp--preset--spacing--20) + 0.75rem)',
			},
		},
		typography: {
			fontFamily: 'var(--wp--preset--font-family--heading)',
			fontSize: 'var(--wp--preset--font-size--large)',
		},
		variations: {
			plain: {
				border: {
					color: 'var(--wp--preset--color--black)',
					radius: '0',
					style: 'solid',
					width: '0',
				},
				color: {
					background: 'transparent',
				},
				spacing: {
					padding: {
						bottom: 'var(--wp--preset--spacing--20)',
						left: 'var(--wp--preset--spacing--20)',
						right: 'var(--wp--preset--spacing--20)',
						top: 'var(--wp--preset--spacing--20)',
					},
				},
				typography: {
					fontFamily: 'var(--wp--preset--font-family--body)',
					fontStyle: 'normal',
					fontSize: 'var(--wp--preset--font-size--medium)',
					lineHeight: '1.5',
				},
			},
		},
	},
	'core/search': {
		css: '& .wp-block-search__input{border-radius:.33rem}',
		typography: {
			fontSize: 'var(--wp--preset--font-size--small)',
		},
		elements: {
			button: {
				border: {
					radius: { ref: 'styles.elements.button.border.radius' },
				},
			},
		},
	},
	'core/separator': {
		border: {
			color: 'currentColor',
			style: 'solid',
			width: '0 0 1px 0',
		},
		color: {
			text: 'var(--wp--preset--color--black)',
		},
		css: ' &:not(.is-style-wide):not(.is-style-dots):not(.alignwide):not(.alignfull){width: var(--wp--preset--spacing--60)}',
	},
	'core/site-tagline': {
		color: {
			text: 'var(--wp--preset--color--black)',
		},
		typography: {
			fontSize: 'var(--wp--preset--font-size--small)',
		},
	},
	'core/site-title': {
		elements: {
			link: {
				':hover': {
					typography: {
						textDecoration: 'none',
					},
				},
				typography: {
					textDecoration: 'none',
				},
			},
		},
		typography: {
			fontFamily: 'var(--wp--preset--font-family--body)',
			fontSize: '1.2rem',
			fontStyle: 'normal',
			fontWeight: '600',
		},
	},
};

export default blocks;
