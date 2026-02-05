export default function (plop) {
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
				name: 'textdomain',
				message: 'What is your theme\'s text domain?',
				default: 'wp-starter',
				validate: function (value) {
					if (/.+/.test(value)) {
					  return true;
					}
					return "Theme text domain is required";
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
					base: 'src/plop-templates',
					templateFiles: 'src/plop-templates/*.hbs',
					abortOnFail: true,
				},
			];

			// add styles
			if (data.styles) {
				actions.push({
					type: 'modify',
					path: 'blocks/{{dashCase name}}/block.json',
					pattern: /-- STYLES HERE --/gi,
					templateFile: 'src/plop-templates/parts/styles.json.hbs'
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
			if (data.variations) {
				actions.push({
					type: 'modify',
					path: 'blocks/{{dashCase name}}/block.json',
					pattern: /-- VARIATIONS HERE --/gi,
					templateFile: 'src/plop-templates/parts/variations.json.hbs'
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
