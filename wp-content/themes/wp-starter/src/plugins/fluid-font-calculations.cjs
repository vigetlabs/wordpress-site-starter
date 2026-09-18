/**
 * Firefox-safe fluid font clamps: replaces length/length calc (invalid in Firefox)
 * with clamp(min, calc(interceptPx + slopeVw * 1vw), max).
 *
 * Reads breakpoint max from tailwind @theme (--breakpoint-container) and ranges
 * from font-sizes.css. See plan: bpMax must match @theme; --cfg-fluid-bp-max in
 * CSS may still use WP vars for documentation.
 */

const fs = require('fs');
const path = require('path');
const { SIZE_SUFFIXES, SUPPORTED_FILTERS } = require('../theme-json/helpers/font-scale.cjs');

const THEME_ROOT = path.join(__dirname, '..', '..');

/**
 * The output custom property for a token: filter-prefixed tokens (e.g. `headline-lg`) get
 * `--fluid-{token}`, everything else (the body scale) gets `--fluid-text-{token}`. Suffixes in
 * SIZE_SUFFIXES never contain a hyphen, so any hyphen in the token means it's filter-prefixed.
 *
 * @param {string} token
 */
function propForToken(token) {
	return token.includes('-') ? `--fluid-${token}` : `--fluid-text-${token}`;
}

/**
 * Checks SIZE_SUFFIXES once with no prefix (the body scale), then once per SUPPORTED_FILTERS
 * prefix (e.g. `headline-lg`), keeping only the combinations that have a --fs-{token}-min/max
 * range actually defined in font-sizes.css.
 *
 * @param {string} src font-sizes.css content
 * @returns {string[]}
 */
function discoverFluidTokens(src) {
	const hasRange = (token) =>
		new RegExp(`--fs-${token}-min:`).test(src) && new RegExp(`--fs-${token}-max:`).test(src);

	const tokens = SIZE_SUFFIXES.filter(hasRange);

	for (const filter of SUPPORTED_FILTERS) {
		for (const suffix of SIZE_SUFFIXES) {
			const token = `${filter}-${suffix}`;
			if (hasRange(token)) {
				tokens.push(token);
			}
		}
	}

	return tokens;
}

/** @param {string} themeRoot */
function readFontSizes(themeRoot) {
	const p = path.join(themeRoot, 'src/styles/tailwind/inc/font-sizes.css');
	const src = fs.readFileSync(p, 'utf8');
	const bpMinM = src.match(/--cfg-fluid-bp-min:\s*(\d+)/);
	if (!bpMinM) {
		throw new Error('[fluid-font-calculations] Could not parse --cfg-fluid-bp-min from font-sizes.css');
	}
	const bpMin = parseInt(bpMinM[1], 10);
	const tokens = discoverFluidTokens(src);
	const ranges = {};
	for (const token of tokens) {
		const minM = src.match(new RegExp(`--fs-${token}-min:\\s*([^;]+);`, 'm'));
		const maxM = src.match(new RegExp(`--fs-${token}-max:\\s*([^;]+);`, 'm'));
		ranges[token] = { min: minM[1].trim(), max: maxM[1].trim() };
	}
	return { bpMin, tokens, ranges };
}

/** @param {string} themeRoot */
function readBreakpointContainerPx(themeRoot) {
	const p = path.join(themeRoot, 'src/styles/tailwind/tailwind.css');
	const src = fs.readFileSync(p, 'utf8');
	const m = src.match(/--breakpoint-container:\s*([\d.]+)px/);
	if (!m) {
		throw new Error('[fluid-font-calculations] Could not parse --breakpoint-container from tailwind.css');
	}
	return parseFloat(m[1]);
}

/**
 * @param {string} length rem or px string
 * @param {number} rootPx
 */
function lengthToPx(length, rootPx) {
	const s = length.trim();
	const rem = s.match(/^([\d.]+)rem$/i);
	if (rem) return parseFloat(rem[1]) * rootPx;
	const px = s.match(/^([\d.]+)px$/i);
	if (px) return parseFloat(px[1]);
	throw new Error(`[fluid-font-calculations] Unsupported length (use rem or px): ${length}`);
}

function round(n, digits = 4) {
	const f = 10 ** digits;
	return Math.round(n * f) / f;
}

/**
 * @param {{ bpMin: number, bpMax: number, rootPx?: number, themeRoot?: string }} opts
 * @returns {Map<string, string>} prop -> full clamp() value
 */
function buildFluidTextReplacements(opts) {
	const { bpMin, bpMax, rootPx = 16, themeRoot = THEME_ROOT } = opts;
	if (bpMax <= bpMin) {
		throw new Error(`[fluid-font-calculations] bpMax (${bpMax}) must be > bpMin (${bpMin})`);
	}
	const { tokens, ranges } = readFontSizes(themeRoot);
	const rangePx = bpMax - bpMin;
	const map = new Map();

	for (const token of tokens) {
		const { min, max } = ranges[token];
		const minPx = lengthToPx(min, rootPx);
		const maxPx = lengthToPx(max, rootPx);
		const deltaF = maxPx - minPx;
		const slope = deltaF / rangePx;
		const slopeVw = round(slope * 100, 4);
		const intercept = round(minPx - slope * bpMin, 4);
		const mid =
			deltaF === 0
				? min
				: `calc(${intercept}px + ${slopeVw}vw)`;
		const clampVal = `clamp(var(--fs-${token}-min), ${mid}, var(--fs-${token}-max))`;
		map.set(propForToken(token), clampVal);
	}
	return map;
}

/** @param {string} varRef e.g. var(--fluid-text-lg) or var(--fluid-headline-lg) */
function tokenFromFluidVar(varRef) {
	const m = String(varRef).trim().match(/^var\(\s*(--fluid-[\w-]+)\s*\)$/i);
	return m ? m[1] : null;
}

/**
 * Map of CSS custom property -> full clamp string (for PostCSS + theme.json).
 * @param {string} [themeRoot]
 */
function getFluidTextDeclMap(themeRoot = THEME_ROOT) {
	const bpMin = readFontSizes(themeRoot).bpMin;
	const bpMax = readBreakpointContainerPx(themeRoot);
	return buildFluidTextReplacements({ bpMin, bpMax, themeRoot });
}

function postcssPlugin() {
	return {
		postcssPlugin: 'postcss-ff-fluid-fonts',
		Once(root) {
			const declMap = getFluidTextDeclMap();
			root.walkDecls((decl) => {
				if (decl.prop === '--fluid-t') {
					decl.remove();
					return;
				}
				const next = declMap.get(decl.prop);
				if (next) {
					decl.value = next;
				}
			});
		},
	};
}
postcssPlugin.postcss = true;

module.exports = {
	THEME_ROOT,
	SIZE_SUFFIXES,
	SUPPORTED_FILTERS,
	discoverFluidTokens,
	readFontSizes,
	readBreakpointContainerPx,
	buildFluidTextReplacements,
	getFluidTextDeclMap,
	tokenFromFluidVar,
	postcssPlugin,
};
