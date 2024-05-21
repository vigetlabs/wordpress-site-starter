/* Because we can not access the mark up of the dropdown button we have to add it to the DOM */
document.addEventListener('alpine:init', () => {
	addAlpineToHTML()
})

function addAlpineToHTML() {
	const buttons = document.querySelectorAll(".wp-block-navigation-submenu__toggle")
	const submenus = document.querySelectorAll(".wp-block-navigation__submenu-container.wp-block-navigation-submenu")

	buttons.forEach(element => {
		element.setAttribute('x-ref', 'button')
		element.setAttribute('x-on:click', 'toggle()')
		element.setAttribute(':aria-expanded', 'open')
		element.setAttribute(':aria-controls', '$id("dropdown-button")')
	});

	submenus.forEach(element => {
		element.setAttribute('x-ref', 'panel')
		element.setAttribute('x-show', 'open')
		element.setAttribute('x-trap', 'open')
		element.setAttribute('x-transition.origin.top.left', '')
		element.setAttribute('x-on:click.outside', 'close($refs.button)')
		element.setAttribute(':id', '$id("dropdown-button")')
	});
}

export default () => ({
    open: false,
	toggle() {
		if (this.open) {
			return this.close()
		}

		this.$refs.button.focus()

		this.open = true
	},
	close(focusAfter) {
		if (! this.open) return

		this.open = false

		focusAfter && focusAfter.focus()
	}
})
