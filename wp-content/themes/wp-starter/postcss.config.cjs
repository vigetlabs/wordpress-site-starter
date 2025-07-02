module.exports = {
	plugins: {
		'postcss-import-ext-glob': {},
		'postcss-import': {},
		tailwindcss: {},
		autoprefixer: {},
		...(process.env.NODE_ENV === 'prod' ? { cssnano: {} } : {})
  },
}
