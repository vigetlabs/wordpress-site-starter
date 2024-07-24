module.exports = function (plop) {
	// controller generator
	plop.setGenerator('block', {
		description: 'Set up a new block',
		prompts: [
			{
				type: 'input',
				name: 'name',
				message: 'What is the block name?'
			},
		],
		actions: [{
			type: 'addMany',
			destination: 'blocks/{{name}}/',
			base: 'plop-templates',
			templateFiles: 'plop-templates/*.hbs'
		}]
	});
};
