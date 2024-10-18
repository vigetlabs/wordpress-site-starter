const plugin = require("tailwindcss/plugin")

module.exports = plugin.withOptions(function (options = {}) {
	return function({ addComponents }) {
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
			'.btn-default': {
				...base,
				[`@apply
				bg-black text-white
				hover:bg-black/85
				active:bg-black/70
				focus-visible:bg-black focus-visible:ring-black/50`]: {},
			},

			'.btn-default-light': {
				...base,
				[`@apply
				bg-white text-black
				hover:bg-white/85
				active:bg-white/70
				focus-visible:bg-white/10 focus-visible:ring-white/15`]: {},
			},

			'.btn-outline': {
				...base,
				[`@apply
				border border-current bg-transparent text-black
				hover:bg-black/5 hover:border-black hover:text-black
				active:bg-black/15 active:text-black
				focus-visible:bg-black/10 focus-visible:ring-black/15`]: {},
			},

			'.btn-outline-light': {
				...base,
				[`@apply
				border-current text-white bg-transparent
				hover:bg-white/15
				active:bg-white/25
				focus-visible:bg-black/30 focus-visible:ring-white/50`]: {},
			},

			'.btn-text': {
				...base,
				[`@apply
				bg-transparent text-black
				hover:text-black/90 hover:underline
				active:text-black/80
				focus-visible:bg-black focus-visible:ring-black/50`]:
				{},
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

