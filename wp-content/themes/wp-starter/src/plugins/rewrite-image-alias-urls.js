/**
 * Rewrite CSS `url('@images/...')` to a browser-reachable URL.
 *
 * Vite's resolve.alias handles `@images` for CSS that goes through Vite's
 * pipeline. Editor CSS does not: vite-scoped-editor-styles runs its own
 * PostCSS pass and inlines the result into a <style> tag. Unresolved
 * `url('@images/...')` then resolves against the current document URL
 * (`/wp-admin/...` in the block editor).
 *
 * Tailwind `@theme` custom properties can also survive Vite's url rewriter,
 * so production CSS assets get the same rewrite.
 *
 * Maps to the publicDir copy of `src/public/images/`:
 *   serve → {origin}/images/...
 *   build → {base}images/...  (e.g. /wp-content/themes/<slug>/dist/images/...)
 */

const IMAGE_ALIAS_URL_RE = /url\(\s*(['"]?)@images\/([^'")\s]+)\1\s*\)/g;

/**
 * @param {import('vite').ResolvedConfig} config
 * @returns {string}
 */
export function getImagesUrlPrefix(config) {
	if (config.command === 'serve') {
		const origin = String(config.server?.origin ?? '').replace(/\/$/, '');
		return `${origin}/images/`;
	}

	return `${String(config.base ?? '/').replace(/\/$/, '')}/images/`;
}

/**
 * @param {string} code
 * @param {string} prefix
 * @returns {string}
 */
export function rewriteImageAliasUrls(code, prefix) {
	if (!prefix || !code.includes('@images/')) {
		return code;
	}

	return code.replace(
		IMAGE_ALIAS_URL_RE,
		(_, quote, file) => `url(${quote}${prefix}${file}${quote})`
	);
}

/**
 * Vite plugin: rewrite `@images` in transformed modules and emitted CSS.
 */
export default function rewriteImagesAlias() {
	let prefix = '';

	return {
		name: 'rewrite-images-alias',

		configResolved(config) {
			prefix = getImagesUrlPrefix(config);
		},

		transform: {
			order: 'post',
			handler(code, id) {
				if (id.includes('node_modules') || !code.includes('@images/')) {
					return;
				}

				const rewritten = rewriteImageAliasUrls(code, prefix);
				if (rewritten === code) {
					return;
				}

				return { code: rewritten, map: null };
			},
		},

		generateBundle(_options, bundle) {
			for (const item of Object.values(bundle)) {
				if (item.type === 'asset' && item.fileName.endsWith('.css')) {
					const source = typeof item.source === 'string'
						? item.source
						: item.source.toString();
					item.source = rewriteImageAliasUrls(source, prefix);
				}

				if (item.type === 'chunk' && item.code.includes('@images/')) {
					item.code = rewriteImageAliasUrls(item.code, prefix);
				}
			}
		},
	};
}
