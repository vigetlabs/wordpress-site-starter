module.exports = {
	plugins: {
		'postcss-import-ext-glob': {},
		'@tailwindcss/postcss': {
			config: './tailwind.config.js',
		},
		...(process.env.NODE_ENV === 'prod' ? { cssnano: {} } : {})
  },
}
