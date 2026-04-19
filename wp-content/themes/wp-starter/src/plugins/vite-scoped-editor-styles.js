/**
 * Vite plugin: virtual:editor-scoped-styles
 *
 * Imported by editor.js. Runs the full PostCSS pipeline on src/styles/main.css
 * and returns a JS module that:
 *   - Injects scoped CSS into the parent admin document (non-iframe editor).
 *     Only `body` and bare element selectors (h1, p, ul …) are prefixed with
 *     `.editor-styles-wrapper` — mirroring WordPress's transformStyles() logic.
 *     Class/ID/attribute/pseudo selectors are left as-is.
 *   - Injects unscoped CSS directly into the Gutenberg editor iframe (no
 *     prefix needed inside the iframe).
 *   - Copies cross-origin font <link> tags into the iframe.
 *
 * HMR: handleHotUpdate() explicitly invalidates the virtual module and returns
 * [mod] so Vite pushes an update to the browser on every CSS change.  Relying
 * solely on addWatchFile() for virtual modules is unreliable — this approach
 * guarantees the update reaches both the non-iframe and iframe editors.
 *
 * Performance: a persistent PostCSS processor keeps @tailwindcss/postcss's
 * IncrementalCompiler alive between HMR cycles. Builds are serialised (at most
 * one in-flight + one queued) to prevent concurrent access to the compiler.
 */

import path from 'path';
import { readFileSync } from 'fs';
import { fileURLToPath } from 'url';
import postcss from 'postcss';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

const VIRTUAL_MODULE_ID = 'virtual:editor-scoped-styles';
const RESOLVED_ID = '\0' + VIRTUAL_MODULE_ID;
const SCOPE = '.editor-styles-wrapper';
const SCOPED_STYLE_ID = 'vite-editor-scoped-styles';
const IFRAME_STYLE_ID = 'vite-editor-iframe-styles';

/**
 * Minimal PostCSS scoping plugin — mirrors WordPress's transformStyles() logic.
 *
 * `body` → `.editor-styles-wrapper`
 * Bare element selectors (h1, p, ul …) → `.editor-styles-wrapper h1`, etc.
 * `:root`, `html`, class/ID/attribute/pseudo selectors → unchanged.
 * Nested rules are skipped (parent already scoped).
 */
function makeScopePlugin(prefix) {
	return {
		postcssPlugin: 'scope-to-editor-wrapper',
		Rule(rule) {
			if (rule.parent?.type === 'rule') return;

			rule.selectors = rule.selectors.map(selector => {
				const s = selector.trim();

				if (s === ':root' || s === 'html') return selector;
				if (s === 'body') return prefix;

				// Tailwind v4 expands CSS nesting using :is()/:where()/:not()/:has()
				// in production builds. These look like pseudo-classes but wrap element
				// selectors that must be scoped, e.g.:
				//   table, .foo { td { ... } }  →  :is(table,.foo) td { ... }
				// Without this check they'd fall through to the "leave as-is" branch.
				if (/^:(?:is|where|not|has)\(/.test(s)) return `${prefix} ${selector}`;

				if (/^[.#[*:&+>~]/.test(s)) return selector;
				if (/^[a-z]/i.test(s)) return `${prefix} ${selector}`;

				return selector;
			});
		},
	};
}

function buildModule(scopedCss, unscopedCss) {
	return `
const scopedCss   = ${JSON.stringify(scopedCss)};
const unscopedCss = ${JSON.stringify(unscopedCss)};

const SCOPED_STYLE_ID  = ${JSON.stringify(SCOPED_STYLE_ID)};
const IFRAME_STYLE_ID  = ${JSON.stringify(IFRAME_STYLE_ID)};
const IFRAME_SELECTOR  = 'iframe[name="editor-canvas"], .editor-canvas__iframe';

function getOrCreateStyle(doc, id) {
	let el = doc.getElementById(id);
	if (!el) {
		el = doc.createElement('style');
		el.id = id;
		doc.head.appendChild(el);
	}
	return el;
}

function injectIntoIframe(iframe) {
	const tryInject = () => {
		try {
			const iframeDoc = iframe.contentDocument || iframe.contentWindow?.document;

			if (!iframeDoc || !iframeDoc.head) {
				iframe.addEventListener('load', tryInject, { once: true });
				return;
			}
			if (iframeDoc.readyState === 'loading') {
				iframeDoc.addEventListener('DOMContentLoaded', tryInject, { once: true });
				return;
			}

			getOrCreateStyle(iframeDoc, IFRAME_STYLE_ID).textContent = unscopedCss;

			// Copy cross-origin font/icon stylesheets that WordPress doesn't transfer.
			document.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
				const href = link.getAttribute('href') || '';
				if (!href) return;
				const isExternal = href.startsWith('https://') && !href.startsWith(window.location.origin);
				const isFontLink  = href.includes('typekit') || href.includes('fonts.googleapis') || href.includes('fonts.gstatic');
				if (isExternal && isFontLink && !iframeDoc.querySelector('link[href="' + href + '"]')) {
					const clone  = iframeDoc.createElement('link');
					clone.rel    = 'stylesheet';
					clone.href   = href;
					if (link.media) clone.media = link.media;
					iframeDoc.head.appendChild(clone);
				}
			});

			// Re-inject after future iframe navigations (about:blank → real page, FSE transitions).
			iframe.addEventListener('load', tryInject, { once: true });
		} catch (_e) {
			iframe.addEventListener('load', tryInject, { once: true });
		}
	};

	tryInject();
}

// wp-block-library uses selectors like:
//   body:not(.editor-styles-wrapper) .wp-block-cover ... { z-index: 1; }
// These were written for the old non-iframe editor and incorrectly override
// theme styles in modern WordPress. Remove them from the CSSOM.
function neutralizeBodyNotEditorRules() {
	function processRules(rules, parent) {
		for (let i = rules.length - 1; i >= 0; i--) {
			const rule = rules[i];
			if (rule.selectorText !== undefined) {
				const parts = rule.selectorText.split(',');
				if (parts.length > 0 && parts.every(s => s.trim().startsWith('body:not(.editor-styles-wrapper)'))) {
					try { parent.deleteRule(i); } catch (_e) {}
				}
			} else if (rule.cssRules) {
				processRules(rule.cssRules, rule);
			}
		}
	}
	function processSheet(sheet) {
		try { if (sheet?.cssRules) processRules(sheet.cssRules, sheet); } catch (_e) {}
	}
	const run = () => Array.from(document.styleSheets).forEach(processSheet);
	if (document.readyState === 'complete') run();
	else window.addEventListener('load', run, { once: true });
}
neutralizeBodyNotEditorRules();

getOrCreateStyle(document, SCOPED_STYLE_ID).textContent = scopedCss;
document.querySelectorAll(IFRAME_SELECTOR).forEach(injectIntoIframe);

if (document.__viteEditorStylesObserver) {
	document.__viteEditorStylesObserver.disconnect();
}
document.__viteEditorStylesObserver = new MutationObserver(() => {
	document.querySelectorAll(IFRAME_SELECTOR).forEach(iframe => {
		const iframeDoc = iframe.contentDocument || iframe.contentWindow?.document;
		if (!iframeDoc?.getElementById(IFRAME_STYLE_ID)) {
			injectIntoIframe(iframe);
		}
	});
});
document.__viteEditorStylesObserver.observe(document.body || document.documentElement, {
	childList: true,
	subtree:   true,
});

if (import.meta.hot) {
	import.meta.hot.accept();
}

export default scopedCss;
`;
}

export default function viteEditorStyles(options = {}) {
	const { cssEntry, watchPaths = [] } = options;

	if (!cssEntry) {
		throw new Error('[vite-scoped-editor-styles] options.cssEntry is required');
	}

	// Persistent processor so @tailwindcss/postcss retains its IncrementalCompiler
	// between HMR cycles, enabling incremental rebuilds instead of full rebuilds.
	let cssProcessor = null;

	let cssVariantsPromise = null;
	let buildInFlight = false;
	let buildPendingAfterCurrent = false;

	async function getCssProcessor() {
		if (cssProcessor) return cssProcessor;

		const [
			{ default: postcssImportExtGlob },
			{ default: tailwindPostcss },
		] = await Promise.all([
			import('postcss-import-ext-glob'),
			import('@tailwindcss/postcss'),
		]);

		cssProcessor = postcss([postcssImportExtGlob(), tailwindPostcss()]);
		return cssProcessor;
	}

	async function buildCssVariants(cssEntryPath) {
		const rawCss = readFileSync(cssEntryPath, 'utf-8');
		const processor = await getCssProcessor();
		const result = await processor.process(rawCss, { from: cssEntryPath });
		const unscopedCss = result.css;

		const deps = new Set(
			result.messages
				.filter(m => m.type === 'dependency' && m.file)
				.map(m => m.file),
		);

		let scopedCss;
		try {
			const scoped = await postcss([makeScopePlugin(SCOPE)]).process(unscopedCss, { from: cssEntryPath });
			scopedCss = scoped.css;
		} catch (err) {
			console.warn('[vite-scoped-editor-styles] Scoping failed, using unscoped CSS:', err.message);
			scopedCss = unscopedCss;
		}

		return { scopedCss, unscopedCss, deps };
	}

	// Serialised build queue: at most one build runs at a time, one more queued.
	// Prevents concurrent access to the persistent PostCSS processor.
	function triggerBuild() {
		if (buildInFlight) {
			buildPendingAfterCurrent = true;
			return cssVariantsPromise;
		}

		buildInFlight = true;
		buildPendingAfterCurrent = false;

		cssVariantsPromise = buildCssVariants(cssEntry)
			.catch(err => {
				cssVariantsPromise = null;
				throw err;
			})
			.finally(() => {
				buildInFlight = false;
				if (buildPendingAfterCurrent) {
					buildPendingAfterCurrent = false;
					triggerBuild();
				}
			});

		return cssVariantsPromise;
	}

	return {
		name: 'vite-scoped-editor-styles',

		buildStart() {
			if (!cssVariantsPromise) triggerBuild();
		},

		resolveId(id) {
			if (id === VIRTUAL_MODULE_ID) return RESOLVED_ID;
		},

		async load(id) {
			if (id !== RESOLVED_ID) return;

			if (!cssVariantsPromise) triggerBuild();

			const { scopedCss, unscopedCss, deps } = await cssVariantsPromise;

			for (const dep of deps) {
				this.addWatchFile(dep);
			}

			return buildModule(scopedCss, unscopedCss);
		},

		handleHotUpdate({ file, modules, server }) {
			if (!file.endsWith('.css')) return;

			triggerBuild();

			// Add the virtual module to Vite's normal update set.
			// Returning an array from handleHotUpdate *replaces* Vite's default
			// behavior, so we spread `modules` (the front-end CSS updates Vite
			// would normally send) and append the virtual module alongside them.
			// Without the spread, front-end CSS HMR is silently suppressed.
			const mod = server.moduleGraph.getModuleById(RESOLVED_ID);
			if (mod) {
				server.moduleGraph.invalidateModule(mod);
				return [...modules, mod];
			}
		},

		configureServer(server) {
			if (watchPaths.length) {
				server.watcher.add(watchPaths);
			}

			// Pre-warm the CSS cache while the page is still loading.
			server.httpServer?.once('listening', () => {
				if (!cssVariantsPromise) triggerBuild();
			});
		},
	};
}
