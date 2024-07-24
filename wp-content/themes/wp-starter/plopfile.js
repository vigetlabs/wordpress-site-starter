module.exports = function (plop) {

	// Block icons from https://developer.wordpress.org/resource/dashicons/
	const WPIcons = [
		"default",
		"align-left",
		"cover-image",
		"embed-video",
		"format-gallery",
		"layout",
		"schedule",
	]

	// Block generator
	plop.setGenerator('block', {
		description: 'Set up a new block',
		prompts: [
			{
				type: 'input',
				name: 'name',
				message: 'What is the block name?',
				validate: function (value) {
					if (/.+/.test(value)) {
					  return true;
					}
					return "Name is required";
				  },
			},
			{
				type: 'list',
				name: 'icon',
				message: 'Pick an WordPress icon for the block',
				choices: WPIcons,
			},
			{
				type: 'confirm',
				name: 'styles',
				message: 'Do you need block styles?'
			},
			{
				type: 'confirm',
				name: 'variations',
				message: 'Do you need block variations?'
			}
		],
		actions: function(data) {
			var actions = [
				{
					type: 'addMany',
					destination: 'blocks/{{dashCase name}}/',
					base: 'plop-templates',
					templateFiles: 'plop-templates/*.hbs',
					abortOnFail: true,
				},
				{
					type: "append",
					path: "src/styles/main.css",
					pattern: /\/\*Everything below this line is automatically generated DO NOT REMOVE\*\//i,
					templateFile: 'plop-templates/parts/css-link.css.hbs'
				},
			];

			// add styles
			if (data.styles) {
				actions.push({
					type: 'modify',
					path: 'blocks/{{dashCase name}}/block.json',
					pattern: /-- PREPEND STYLES HERE --/gi,
					templateFile: 'plop-templates/parts/styles.json.hbs'
				});
			} else {
				actions.push({
					type: 'modify',
					path: 'blocks/{{dashCase name}}/block.json',
					pattern: /-- PREPEND STYLES HERE --/gi
				});
			}

			// add variations
			if (data.styles) {
				actions.push({
					type: 'modify',
					path: 'blocks/{{dashCase name}}/block.json',
					pattern: /-- PREPEND VARIATIONS HERE --/gi,
					templateFile: 'plop-templates/parts/variations.json.hbs'
				});
			} else {
				actions.push({
					type: 'modify',
					path: 'blocks/{{dashCase name}}/block.json',
					pattern: /-- PREPEND VARIATIONS HERE --/gi
				});
			}


            return actions;
        }
	});
};
