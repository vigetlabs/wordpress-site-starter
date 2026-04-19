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

const THEME_ROOT = path.join(__dirname, '..', '..');

const FLUID_TOKENS = ['sm', 'base', 'md', 'lg', 'xl', '2xl'];

/** @param {string} themeRoot */
function readFontSizes(themeRoot) {
	const p = path.join(themeRoot, 'src/styles/tailwind/inc/font-sizes.css');
	const src = fs.readFileSync(p, 'utf8');
	const bpMinM = src.match(/--cfg-fluid-bp-min:\s*(\d+)/);
	if (!bpMinM) {
		throw new Error('[fluid-font-calculations] Could not parse --cfg-fluid-bp-min from font-sizes.css');
	}
	const bpMin = parseInt(bpMinM[1], 10);
	const ranges = {};
	for (const token of FLUID_TOKENS) {
		const minRe = new RegExp(`--fs-${token}-min:\\s*([^;]+);`, 'm');
		const maxRe = new RegExp(`--fs-${token}-max:\\s*([^;]+);`, 'm');
		const minM = src.match(minRe);
		const maxM = src.match(maxRe);
		if (!minM || !maxM) {
			throw new Error(`[fluid-font-calculations] Missing --fs-${token}-min/max in font-sizes.css`);
		}
		ranges[token] = { min: minM[1].trim(), max: maxM[1].trim() };
	}
	return { bpMin, ranges };
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
	const { ranges } = readFontSizes(themeRoot);
	const rangePx = bpMax - bpMin;
	const map = new Map();

	for (const token of FLUID_TOKENS) {
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
		const prop = `--fluid-text-${token}`;
		const clampVal = `clamp(var(--fs-${token}-min), ${mid}, var(--fs-${token}-max))`;
		map.set(prop, clampVal);
	}
	return map;
}

/** @param {string} varRef e.g. var(--fluid-text-lg) */
function tokenFromFluidVar(varRef) {
	const m = String(varRef).trim().match(/^var\(\s*--fluid-text-(\w+)\s*\)$/i);
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
	FLUID_TOKENS,
	readFontSizes,
	readBreakpointContainerPx,
	buildFluidTextReplacements,
	getFluidTextDeclMap,
	tokenFromFluidVar,
	postcssPlugin,
};
