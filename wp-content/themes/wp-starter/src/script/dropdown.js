import Alpine from 'alpinejs';
import persist from '@alpinejs/persist'
window.Alpine = Alpine;

Alpine.data('dropdown', () => ({
    open: true,
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
}))
