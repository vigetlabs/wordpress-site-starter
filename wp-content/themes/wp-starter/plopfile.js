module.exports = function (plop) {
	// controller generator
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
				type: 'confirm',
				name: 'styles',
				message: 'Do you want block styles?'
			},
			{
				type: 'confirm',
				name: 'variations',
				message: 'Do you want block variations?'
			}
		],
		actions: function(data) {
			var actions = [{
				type: 'addMany',
				destination: 'blocks/{{dashCase name}}/',
				base: 'plop-templates',
				templateFiles: 'plop-templates/*.hbs',
				abortOnFail: true,
			}];

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

			// add styles
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
