const plugin = require("tailwindcss/plugin")

module.exports = plugin.withOptions(function (options = {}) {
	return function({ addComponents }) {
		const accentColor = options.accentColor ?? 'sky'

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
				bg-${accentColor}-700 text-white
				hover:bg-${accentColor}-700
				active:bg-${accentColor}-800
				focus-visible:bg-${accentColor}-700 focus-visible:ring-${accentColor}-600/50`]: {},
			},

			'.btn-contained-light': {
				...base,
				[`@apply
				bg-${accentColor}-200 text-${accentColor}-900
				hover:bg-${accentColor}-50
				active:bg-${accentColor}-100
				focus-visible:bg-${accentColor}-50 focus-visible:ring-white/50`]: {},
			},

			'.btn-outlined': {
				...base,
				[`@apply
				border border-current bg-transparent text-${accentColor}-900
				hover:bg-${accentColor}-100 hover:border-${accentColor}-700 hover:text-${accentColor}-700
				active:bg-${accentColor}-200/80 active:text-${accentColor}-800
				focus-visible:bg-${accentColor}-100 focus-visible:border-${accentColor}-700 focus-visible:ring-${accentColor}-600/50`]: {},
			},

			'.btn-outlined-light': {
				...base,
				[`@apply
				border-current text-white bg-transparent
				hover:bg-white/25
				active:bg-white/30
				focus-visible:bg-${accentColor}-100/30 focus-visible:ring-white/50`]: {},
			},

			'.btn-subtle': {
				...base,
				[`@apply
				bg-transparent bg-transparent text-${accentColor}-600
				hover:bg-${accentColor}-100 hover:text-${accentColor}-700
				active:bg-${accentColor}-200/80 active:text-${accentColor}-800
				focus-visible:bg-${accentColor}-100 focus-visible:border-${accentColor}-700 focus-visible:ring-${accentColor}-600/50`]: {},
			},

			'.btn-text': {
				...base,
				[`@apply
				bg-transparent text-${accentColor}-600
				hover:text-${accentColor}-700 hover:underline
				active:text-${accentColor}-800
				focus-visible:bg-${accentColor}-100 focus-visible:ring-${accentColor}-600/50`]:
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

		addComponents(buttons)
	  }
})

