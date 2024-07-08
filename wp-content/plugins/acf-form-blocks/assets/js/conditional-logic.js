/* global mfConditionalFields */
document.addEventListener(
	'DOMContentLoaded',
	function() {
		if (typeof mfConditionalFields !== 'function') {
			return;
		}

		if (!document.querySelector('form.acf-block-form')) {
			return;
		}

		mfConditionalFields(
			'form.acf-block-form',
			{
				rules: 'inline',
				disableHidden: true,
				debug: true,
				depth: 8,
			}
		);
	}
);
