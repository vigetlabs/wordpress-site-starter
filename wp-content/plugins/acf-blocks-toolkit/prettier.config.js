module.exports = {
	arrowParens: 'always',
	bracketSameLine: false,
	bracketSpacing: true,
	htmlWhitespaceSensitivity: 'css',
	printWidth: 80,
	proseWrap: 'preserve',
	semi: true,
	singleQuote: true,
	tabWidth: 2,
	useTabs: true,
	trailingComma: 'all',
	overrides: [
		{
			files: ['*.yml', '*.json'],
			options: {
				useTabs: false,
				tabWidth: 2,
			},
		},
	],
};
