/**
 * Admin JS
 *
 * global acffbAdmin
 *
 * @package ACFFB
 */

document.addEventListener('DOMContentLoaded', function() {
	const testButton = document.getElementById('acffb-integration-test');

	if (!testButton) {
		return;
	}

	testButton.addEventListener('click', function(e) {
		e.preventDefault();
		const integrationId = e.target.getAttribute('data-integration-id');
		const response = document.getElementById('acffb-integration-test-response');
		response.innerHTML = 'Testing...';
		const xhr = new XMLHttpRequest();
		xhr.open('POST', acffbAdmin.ajaxUrl);
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xhr.onload = function() {
			response.innerHTML = JSON.parse(xhr.responseText).data.message;
		};
		xhr.send('action=acffb_integration_test&integrationId=' + integrationId + '&nonce=' + acffbAdmin.nonce);
	});
});
