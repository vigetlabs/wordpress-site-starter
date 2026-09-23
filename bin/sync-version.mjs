#!/usr/bin/env node
/**
 * Manage the starter's release version.
 *
 * packages.json is the only version we track. The theme stays pinned at 0.1.0,
 * because that's the version a generated project starts at - both packages.json
 * and CHANGELOG.md are removed during create-project.
 *
 * Usage:
 *   node bin/sync-version.mjs            Report the current version and verify
 *   node bin/sync-version.mjs 1.2.0      Set packages.json to 1.2.0, then verify
 *   node bin/sync-version.mjs --check    Verify only, exit 1 on a problem
 */

import { readFileSync, writeFileSync, existsSync, readdirSync } from 'node:fs';
import { join, dirname, relative } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = dirname(dirname(fileURLToPath(import.meta.url)));
const SEMVER = /^\d+\.\d+\.\d+(?:-[0-9A-Za-z.-]+)?$/;
const THEME_VERSION = '0.1.0';

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

if (setTo && !check) {
	packages.package.version = setTo;
	writeFileSync(packagesPath, `${JSON.stringify(packages, null, indentOf(packagesPath))}\n`);
	console.log(`packages.json: ${version}`);
}

const problems = [];

// Core's bundled themes also carry a style.css and a package.json, and sort ahead
// of ours. vite.config.js is the one marker only the starter theme has.
const themeDir = readdirSync(join(ROOT, 'wp-content/themes'), { withFileTypes: true })
	.filter((entry) => entry.isDirectory())
	.map((entry) => join(ROOT, 'wp-content/themes', entry.name))
	.find((dir) => existsSync(join(dir, 'style.css')) && existsSync(join(dir, 'vite.config.js')));

if (!themeDir) {
	fail('Could not find the theme directory.');
}

const pinned = [
	jsonTarget(join(themeDir, 'package.json'), (data) => data, 'version'),
	headerTarget(join(themeDir, 'style.css'), /^(Version:\s*)(.+)$/m),
	headerTarget(join(themeDir, 'readme.txt'), /^(Stable tag:\s*)(.+)$/m),
];

for (const target of pinned) {
	const current = target.read();

	if (current !== THEME_VERSION) {
		problems.push(
			`${relative(ROOT, target.path)} is ${current ?? 'missing'}, should stay at ${THEME_VERSION}.`
		);
	}
}

const changelogPath = join(ROOT, 'CHANGELOG.md');

if (!existsSync(changelogPath)) {
	problems.push('CHANGELOG.md is missing.');
} else if (!new RegExp(`^## \\[?v?${escape(version)}\\]?\\s*$`, 'm').test(readFileSync(changelogPath, 'utf8'))) {
	problems.push(`CHANGELOG.md has no entry for ${version}.`);
}

if (problems.length) {
	problems.forEach((problem) => console.error(problem));
	process.exit(1);
}

console.log(`Version ${version} is good. Theme pinned at ${THEME_VERSION}.`);

function jsonTarget(path, locate, key) {
	return {
		path,
		read: () => locate(readJson(path))?.[key],
	};
}

function headerTarget(path, pattern) {
	return {
		path,
		read: () => readFileSync(path, 'utf8').match(pattern)?.[2].trim(),
	};
}

function readJson(path) {
	return JSON.parse(readFileSync(path, 'utf8'));
}

// Match the file's existing indentation so the diff stays to one line.
function indentOf(path) {
	return readFileSync(path, 'utf8').match(/\n([ \t]+)"/)?.[1] ?? 2;
}

function escape(value) {
	return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function fail(message) {
	console.error(message);
	process.exit(1);
}
