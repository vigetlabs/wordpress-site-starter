module.exports = {
	plugins: {
		'postcss-import-ext-glob': {},
		'postcss-import': {},
		'tailwindcss/nesting': 'postcss-nesting',
    	tailwindcss: {},
		autoprefixer: {},
		...(process.env.NODE_ENV === 'prod' ? { cssnano: {} } : {})
  },
}
