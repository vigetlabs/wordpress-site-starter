module.exports = function (plop) {
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
				type: 'input',
				name: 'themeslug',
				message: 'What is the slug for your theme?',
				validate: function (value) {
					if (/.+/.test(value)) {
					  return true;
					}
					return "Theme slug is required";
				  },
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
			},
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
					type: 'addMany',
					destination: 'blocks/{{dashCase name}}/',
					base: 'plop-templates',
					templateFiles: 'plop-templates/patterns/*.hbs',
					abortOnFail: true,
				},
				{
					type: "append",
					path: "src/styles/custom-blocks.css",
					pattern: /\/\*DO NOT REMOVE - Everything below this line is automatically generated\*\//i,
					templateFile: 'plop-templates/parts/css-link.css.hbs'
				},
			];

			// add styles
			if (data.styles) {
				actions.push({
					type: 'modify',
					path: 'blocks/{{dashCase name}}/block.json',
					pattern: /-- STYLES HERE --/gi,
					templateFile: 'plop-templates/parts/styles.json.hbs'
				});
			} else {
				// if no styles remove prepend text
				actions.push({
					type: 'modify',
					path: 'blocks/{{dashCase name}}/block.json',
					pattern: /-- STYLES HERE --/gi
				});
			}

			// add variations
			if (data.styles) {
				actions.push({
					type: 'modify',
					path: 'blocks/{{dashCase name}}/block.json',
					pattern: /-- VARIATIONS HERE --/gi,
					templateFile: 'plop-templates/parts/variations.json.hbs'
				});
			} else {
				actions.push({
					type: 'modify',
					path: 'blocks/{{dashCase name}}/block.json',
					pattern: /-- VARIATIONS HERE --/gi
				});
			}


            return actions;
        }
	});
};
