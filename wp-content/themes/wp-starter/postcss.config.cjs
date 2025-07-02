module.exports = {
	plugins: {
		'postcss-import-ext-glob': {},
		'@tailwindcss/postcss': {},
		...(process.env.NODE_ENV === 'prod' ? { cssnano: {} } : {})
  },
}
