import fs from 'fs';
import path from 'path';
import { getColorObject } from './colors.js';

/**
 * Build-time path: Vite runs from the theme root (`process.cwd()`); `blocks/`
 * lives next to `src/` at the theme root.
 */
const THEME_BLOCKS_DIR = path.join(process.cwd(), 'blocks');

/**
 * Read every theme `block.json` keyed by `acf/<block-name>`.
 *
 * @returns {Record<string, object>}
 */
function getThemeBlocks() {
	if (!fs.existsSync(THEME_BLOCKS_DIR)) {
		return {};
	}

	const blocks = {};

	for (const entry of fs.readdirSync(THEME_BLOCKS_DIR, { withFileTypes: true })) {
		if (!entry.isDirectory()) {
			continue;
		}

		const blockJsonPath = path.join(THEME_BLOCKS_DIR, entry.name, 'block.json');
		if (!fs.existsSync(blockJsonPath)) {
			continue;
		}

		try {
			const block = JSON.parse(fs.readFileSync(blockJsonPath, 'utf8'));
			if (!block?.name) {
				continue;
			}

			blocks[`acf/${block.name}`] = block;
		} catch (error) {
			console.warn(`Unable to parse ${blockJsonPath}`, error);
		}
	}

	return blocks;
}

/**
 * Resolve a single palette entry. Objects pass through as-is; strings are
 * looked up in the theme palette (colors-palette.css + colors-utilities.css,
 * after `dark-` slug resolution). Unresolved strings are skipped with a
 * build-time warning.
 *
 * @param {unknown} entry
 * @param {string}  blockName
 * @returns {object|null}
 */
function resolvePaletteEntry(entry, blockName) {
	if (entry && typeof entry === 'object') {
		return entry;
	}

	if (typeof entry === 'string' && entry.length > 0) {
		const colorObject = getColorObject(entry);
		if (colorObject) {
			return colorObject;
		}

		console.warn(
			`[theme.json] Unable to resolve palette slug "${entry}" for block "${blockName}". Entry will be skipped.`,
		);
	}

	return null;
}

/**
 * De-duplicate palette entries by `slug` (fallback to JSON equality for entries
 * without a slug). Earlier entries win, preserving the merge order.
 *
 * @param {object[]} items
 * @returns {object[]}
 */
function dedupePalette(items) {
	const bySlug = new Map();
	const extras = [];
	const seenJson = new Set();

	for (const item of items) {
		if (!item || typeof item !== 'object') {
			continue;
		}

		if (typeof item.slug === 'string' && item.slug.length > 0) {
			if (!bySlug.has(item.slug)) {
				bySlug.set(item.slug, item);
			}
			continue;
		}

		try {
			const json = JSON.stringify(item);
			if (seenJson.has(json)) {
				continue;
			}
			seenJson.add(json);
		} catch {
			// Fall through: push the entry anyway.
		}

		extras.push(item);
	}

	return [...bySlug.values(), ...extras];
}

/**
 * Normalize a block.json `custom` boolean for theme.json output.
 *
 * @param {unknown} value
 * @returns {boolean|undefined}
 */
function booleanOrUndefined(value) {
	if (typeof value !== 'boolean') {
		return undefined;
	}
	return value;
}

function isPlainObject(value) {
	return (
		value !== null &&
		typeof value === 'object' &&
		!Array.isArray(value)
	);
}

/**
 * Recursively merge two block-setting objects. Values from **generated**
 * (from `block.json`) win on conflicts; nested plain objects merge.
 * Arrays and primitives from generated replace entirely.
 *
 * @param {unknown} base - Typically static entries from `settings/blocks.js`
 * @param {unknown} generated - Output from {@link getThemeBlockSettings}
 * @returns {unknown}
 */
function deepMergePreferGenerated(base, generated) {
	if (generated === undefined || generated === null) {
		return base;
	}
	if (base === undefined || base === null) {
		return generated;
	}
	if (Array.isArray(generated) || Array.isArray(base)) {
		return generated;
	}
	if (!isPlainObject(base) || !isPlainObject(generated)) {
		return generated;
	}

	const out = { ...base };
	for (const key of Object.keys(generated)) {
		out[key] = deepMergePreferGenerated(base[key], generated[key]);
	}
	return out;
}

/**
 * Merge static `settings.blocks.js` config with block-json-generated settings.
 * For each block name present in either source, entries are deep-merged and
 * **`block.json`-derived settings take priority** over manual JS config.
 *
 * @param {Record<string, object>} manualBlocks
 * @param {Record<string, object>} generatedBlocks
 * @returns {Record<string, object>}
 */
export function mergeThemeBlocksSettings(manualBlocks, generatedBlocks) {
	const manual = manualBlocks || {};
	const generated = generatedBlocks || {};
	const keys = new Set([
		...Object.keys(manual),
		...Object.keys(generated),
	]);
	const out = {};

	for (const blockName of keys) {
		const m = manual[blockName];
		const g = generated[blockName];
		if (m !== undefined && g !== undefined) {
			out[blockName] = deepMergePreferGenerated(m, g);
		} else {
			out[blockName] = g !== undefined ? g : m;
		}
	}

	return out;
}

/**
 * Build per-block `settings.blocks[<block>]` entries from each theme
 * `block.json`. The block's `settings` object is passed through as-is (merged
 * into the output), while `palette` entries from `settings` and
 * `supports.color.palette` are merged, resolved, de-duplicated, and written to
 * `color.palette`.
 *
 * `color.custom`: `settings.color.custom` and `supports.color.custom` both map
 * to theme.json `color.custom`. If both are set, **`settings.color.custom`
 * wins** (same meaning; explicit theme-json intent overrides block supports).
 *
 * @returns {Record<string, object>}
 */
function getThemeBlockSettings() {
	const blocks = getThemeBlocks();
	const output = {};

	for (const blockName in blocks) {
		const block = blocks[blockName];
		const rawSettings =
			block?.settings && typeof block.settings === 'object' ? block.settings : {};
		const supportsColor =
			block?.supports?.color && typeof block.supports.color === 'object'
				? block.supports.color
				: {};

		const settings = JSON.parse(JSON.stringify(rawSettings));

		// Palette can be declared flat (`settings.palette`) or nested
		// (`settings.color.palette`); strip both so the merged result can be
		// written to the canonical `color.palette` location below.
		const flatPalette = Array.isArray(settings.palette) ? settings.palette : [];
		delete settings.palette;

		const nestedColor =
			settings.color && typeof settings.color === 'object' ? settings.color : null;
		const nestedPalette =
			nestedColor && Array.isArray(nestedColor.palette) ? nestedColor.palette : [];
		const settingsCustom = booleanOrUndefined(nestedColor?.custom);
		if (nestedColor) {
			delete nestedColor.palette;
			delete nestedColor.custom;
		}

		const supportsPalette = Array.isArray(supportsColor.palette)
			? supportsColor.palette
			: [];

		const paletteEntries = [...flatPalette, ...nestedPalette, ...supportsPalette];
		const resolvedPalette = paletteEntries
			.map((item) => resolvePaletteEntry(item, blockName))
			.filter(Boolean);
		const mergedPalette = dedupePalette(resolvedPalette);

		const supportsCustom = booleanOrUndefined(supportsColor.custom);
		let resolvedCustom = settingsCustom;
		if (resolvedCustom === undefined) {
			resolvedCustom = supportsCustom;
		}

		const colorAdditions = {};
		if (mergedPalette.length > 0) {
			colorAdditions.palette = mergedPalette;
		}
		if (resolvedCustom !== undefined) {
			colorAdditions.custom = resolvedCustom;
		}

		const hasNestedColorData = nestedColor && Object.keys(nestedColor).length > 0;

		if (Object.keys(colorAdditions).length > 0 || hasNestedColorData) {
			settings.color = { ...(nestedColor || {}), ...colorAdditions };
		} else if (nestedColor) {
			delete settings.color;
		}

		if (Object.keys(settings).length > 0) {
			output[blockName] = settings;
		}
	}

	return output;
}

export { getThemeBlocks, getThemeBlockSettings };
