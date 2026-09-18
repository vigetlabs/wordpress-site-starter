#!/usr/bin/env node
/**
 * Sync every version string to the project version in packages.json.
 *
 * Usage:
 *   node bin/sync-version.mjs            Write the version everywhere
 *   node bin/sync-version.mjs 1.2.0      Set packages.json to 1.2.0 first, then write
 *   node bin/sync-version.mjs --check    Report mismatches, exit 1 if any (no writes)
 */

import { readFileSync, writeFileSync, existsSync, readdirSync } from 'node:fs';
import { join, dirname, relative } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = dirname(dirname(fileURLToPath(import.meta.url)));
const SEMVER = /^\d+\.\d+\.\d+(?:-[0-9A-Za-z.-]+)?$/;

const args = process.argv.slice(2);
const check = args.includes('--check');
const setTo = args.find((arg) => !arg.startsWith('-'));

if (setTo && !SEMVER.test(setTo)) {
	fail(`"${setTo}" is not a semver version.`);
}

const packagesPath = join(ROOT, 'packages.json');
const packages = readJson(packagesPath);
const version = setTo ?? packages.package?.version;

if (!version) {
	fail('No version found in packages.json.');
}

// The theme is the only one in wp-content/themes with a package.json next to its style.css.
const themeDir = readdirSync(join(ROOT, 'wp-content/themes'), { withFileTypes: true })
	.filter((entry) => entry.isDirectory())
	.map((entry) => join(ROOT, 'wp-content/themes', entry.name))
	.find((dir) => existsSync(join(dir, 'style.css')) && existsSync(join(dir, 'package.json')));

if (!themeDir) {
	fail('Could not find the theme directory.');
}

const targets = [
	jsonTarget(packagesPath, (data) => data.package, 'version'),
	jsonTarget(join(themeDir, 'package.json'), (data) => data, 'version'),
	headerTarget(join(themeDir, 'style.css'), /^(Version:\s*)(.+)$/m),
	headerTarget(join(themeDir, 'readme.txt'), /^(Stable tag:\s*)(.+)$/m),
];

let changed = 0;

for (const target of targets) {
	const current = target.read();

	if (current === version) {
		continue;
	}

	changed++;
	console.log(`${relative(ROOT, target.path)}: ${current ?? 'missing'} -> ${version}`);

	if (!check) {
		target.write(version);
	}
}

if (check && changed) {
	fail(`${changed} file(s) out of sync with ${version}.`);
}

console.log(changed ? `Synced to ${version}.` : `Already at ${version}.`);

function jsonTarget(path, locate, key) {
	return {
		path,
		read: () => locate(readJson(path))?.[key],
		write: (value) => {
			const data = readJson(path);
			locate(data)[key] = value;
			writeFileSync(path, `${JSON.stringify(data, null, indentOf(path))}\n`);
		},
	};
}

function headerTarget(path, pattern) {
	return {
		path,
		read: () => readFileSync(path, 'utf8').match(pattern)?.[2].trim(),
		write: (value) => {
			const contents = readFileSync(path, 'utf8');

			if (!pattern.test(contents)) {
				fail(`No match for ${pattern} in ${relative(ROOT, path)}.`);
			}

			writeFileSync(path, contents.replace(pattern, `$1${value}`));
		},
	};
}

function readJson(path) {
	return JSON.parse(readFileSync(path, 'utf8'));
}

// Match the file's existing indentation so the diff stays to one line.
function indentOf(path) {
	return readFileSync(path, 'utf8').match(/\n([ \t]+)"/)?.[1] ?? 2;
}

function fail(message) {
	console.error(message);
	process.exit(1);
}
