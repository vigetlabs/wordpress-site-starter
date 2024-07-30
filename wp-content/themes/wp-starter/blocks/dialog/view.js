// trigger dialog elements to open when adjacent button is clicked
const dialogButtons = document.querySelectorAll('dialog + button');
dialogButtons.forEach(button => {
	button.addEventListener('click', (e) => {
		// set dialog to the previous sibling dialog element
		const dialog = button.previousElementSibling;
		if (!dialog || dialog.tagName.toLowerCase() !== 'dialog') {
			return;
		}

		e.preventDefault();
		e.stopPropagation();

		dialog.showModal();
	});
});

// close dialog when clicked outside of it
const dialogs = document.querySelectorAll('dialog');
document.addEventListener('click', ({ target }) => {
	dialogs.forEach(dialog => {
		if ( !dialog.open ) {
			return;
		}
		if (target === dialog || !dialog.contains(target)) {
			dialog.close();
		}
	});
});



