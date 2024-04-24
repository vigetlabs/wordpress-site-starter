const plugin = require("tailwindcss/plugin")

module.exports = plugin(({ addComponents, addVariant }) => {
	// Base Styles
	const base = {
		// core
		'@apply inline-flex items-center rounded transition text-base gap-12':
		{},
		// focus
		'@apply focus:outline-none focus-visible:ring-4': {},
		// disabled
		'@apply disabled:opacity-20 disabled:pointer-events-none': {},
		// icon
		'& svg': {
		'@apply size-20': {},
		},
		// text exempt
		'&:not(.btn-text)': {
		'@apply px-20 min-h-40': {},
		},
	}

	const buttons = {
		// Button Variants
		'.btn-contained': {
			...base,
			[`@apply
			bg-sky-700 text-white
			hover:bg-sky-700
			active:bg-sky-800
			focus-visible:bg-sky-700 focus-visible:ring-sky-600/50`]: {},
		},

		'.btn-contained-light': {
			...base,
			[`@apply
			bg-sky-200 text-sky-900
			hover:bg-sky-50
			active:bg-sky-100
			focus-visible:bg-sky-50 focus-visible:ring-white/50`]: {},
		},

		'.btn-outlined': {
			...base,
			[`@apply
			border border-sky-900 bg-transparent text-sky-900
			hover:bg-sky-100 hover:border-sky-700 hover:text-sky-700
			active:bg-sky-200/80 active:text-sky-800
			focus-visible:bg-sky-100 focus-visible:border-sky-700 focus-visible:ring-sky-600/50`]: {},
		},

		'.btn-outlined-light': {
			...base,
			[`@apply
			border-white text-white
			hover:bg-white/25
			active:bg-white/30
			focus-visible:bg-sky-100/30 focus-visible:ring-white/50`]: {},
		},

		'.btn-subtle': {
			...base,
			[`@apply
			bg-transparent bg-transparent text-sky-600
			hover:bg-sky-100 hover:text-sky-700
			active:bg-sky-200/80 active:text-sky-800
			focus-visible:bg-sky-100 focus-visible:border-sky-700 focus-visible:ring-sky-600/50
			has-background:!text-white
			has-background:hover:bg-white/25
			has-background:active:bg-white/30
			has-background:focus-visible:bg-sky-100/30 has-background:focus-visible:ring-white/75`]: {},
		},

		'.btn-text': {
			...base,
			[`@apply
			bg-transparent text-sky-600
			hover:text-sky-700 hover:underline
			active:text-sky-800
			focus-visible:bg-sky-100 focus-visible:ring-sky-600/50
			has-background:border-white has-background:text-white
			has-background:hover:text-white/90
			has-background:active:text-white/80
			has-background:focus-visible:bg-sky-100/30  has-background:focus-visible:ring-white/75`]:
			{},
		},

		// Button Sizes
		'.btn-sm': {
			'@apply text-sm gap-8': {},
			'& svg': {
			'@apply size-16': {},
			},
			'&:not(.btn-text)': {
			'@apply px-16 min-h-32': {},
			},
		},
		'.btn-lg': {
			'@apply text-xl gap-12': {},
			'& svg': {
			'@apply size-24': {},
			},
			'&:not(.btn-text)': {
			'@apply px-32 min-h-56': {},
			},
		},

		// Button Icon/Sizes
		'.btn-icon': {
			fontSize: '0 !important',
			lineHeight: '0 !important',
			'@apply !p-0 !gap-0 aspect-square justify-center items-center': {},
		},
	}
	addVariant("has-background", ".has-background &")
	addComponents(buttons)
})
