/**
 * Adds the front end's template body classes to the block editor canvas, so
 * page-scoped styles (e.g. `.post-type-archive-{type} ...`) apply while editing.
 *
 * @package VigetWP
 */

(function (wp) {
	if (!wp?.data) {
		return;
	}

	const CANVAS = 'iframe[name="editor-canvas"]';

	// Body classes WordPress adds on the front end for the template or post being edited.
	const getClasses = () => {
		const editor = wp.data.select('core/editor');
		const type = editor?.getCurrentPostType();

		if (!type) {
			return [];
		}

		if ('wp_template' === type) {
			const slug = String(editor.getCurrentPostId() ?? '').split('//').pop();
			const [, kind, postType] = slug.match(/^(archive|single)-(.+)$/) ?? [];

			if ('archive' === kind) {
				return ['archive', 'post-type-archive', `post-type-archive-${postType}`];
			}

			if ('single' === kind) {
				return ['single', `single-${postType}`];
			}

			return [];
		}

		return 'page' === type ? ['page'] : ['single', `single-${type}`];
	};

	const apply = () => {
		const body = document.querySelector(CANVAS)?.contentDocument?.body;

		if (!body) {
			return;
		}

		const next = getClasses();
		const previous = (body.dataset.vigetwpBodyClasses ?? '').split(' ').filter(Boolean);

		if (previous.join(' ') === next.join(' ') && next.every((name) => body.classList.contains(name))) {
			return;
		}

		body.classList.remove(...previous);
		body.classList.add(...next);
		body.dataset.vigetwpBodyClasses = next.join(' ');
	};

	wp.data.subscribe(apply);

	// The canvas iframe reloads on its own (template switches, device previews).
	new MutationObserver(() => {
		document.querySelectorAll(CANVAS).forEach((iframe) => {
			if (!iframe.dataset.vigetwpBodyClassesBound) {
				iframe.dataset.vigetwpBodyClassesBound = '1';
				iframe.addEventListener('load', apply);
			}
		});

		apply();
	}).observe(document.body, { childList: true, subtree: true });
})(window.wp);
